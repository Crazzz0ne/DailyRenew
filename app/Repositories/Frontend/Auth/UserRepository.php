<?php

namespace App\Repositories\Frontend\Auth;

use App\Events\Frontend\Auth\UserConfirmed;
use App\Events\Frontend\Auth\UserProviderRegistered;
use App\Exceptions\GeneralException;
use App\Models\Auth\SocialAccount;
use App\Models\Auth\User;
use App\Notifications\Frontend\Auth\UserNeedsConfirmation;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * @param $token
     *
     * @return bool|Model
     */
    public function findByPasswordResetToken($token)
    {
        foreach (DB::table(config('auth.passwords.users.table'))->get() as $row) {
            if (password_verify($token, $row->token)) {
                return $this->getByColumn($row->email, 'email');
            }
        }

        return false;
    }

    /**
     * @param $uuid
     *
     * @return mixed
     * @throws GeneralException
     */
    public function findByUuid($uuid)
    {
        $user = $this->model
            ->uuid($uuid)
            ->first();

        if ($user instanceof $this->model) {
            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.not_found'));
    }

    /**
     * @param array $data
     *
     * @return Model|mixed
     * @throws Throwable
     * @throws Exception
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = parent::create([
                'first_name' => ucfirst($data['first_name']),
                'last_name' => ucfirst($data['last_name']),
                'remote_option' => false,
                'phone_number' => $data['phone_number'],
                'email' => $data['email'],
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
                'active' => true,
                'password' => $data['password'],
                // If users require approval or needs to confirm email
                'confirmed' => true,
            ]);

            if ($user) {
                // Add the default site role to the new user
                $user->assignRole(config('access.users.default_role'));
            }

            /*
             * If users have to confirm their email and this is not a social account,
             * and the account does not require admin approval
             * send the confirmation email
             *
             * If this is a social account they are confirmed through the social provider by default
             */


            // Return the user object
            return $user;
        });
    }

    /**
     * @param       $id
     * @param array $input
     * @param bool|UploadedFile $image
     *
     * @return array|bool
     * @throws GeneralException
     */
    public function update($id, array $input, $image = false)
    {
        $user = $this->getById($id);
        $user->first_name = $input['first_name'];
        $user->last_name = $input['last_name'];
        $user->avatar_type = $input['avatar_type'];
        $user->phone_number = $input['phone_number'];
        $user->remote_option = $input['remote_option'];

        // Upload profile image if necessary $image
        if ($image) {
            $filename = $this->resizeAvatar($image);

//dd($resizedImage);

            $user->avatar_location = Storage::disk('s3')->putFileAs('user/avatar', $filename, $user->first_name . '' . $id);
        } else {
            // No image being passed
            if ($input['avatar_type'] === 'storage') {
                // If there is no existing image
                if (auth()->user()->avatar_location === '') {
                    throw new GeneralException('You must supply a profile image.');
                }
            } else {
                // If there is a current image, and they are not using it anymore, get rid of it
                if (auth()->user()->avatar_location !== '') {
                    Storage::disk('public')->delete(auth()->user()->avatar_location);
                }

                $user->avatar_location = null;
            }
        }

        if ($user->canChangeEmail()) {
            //Address is not current address so they need to reconfirm
            if ($user->email !== $input['email']) {
                //Emails have to be unique
                if ($this->getByColumn($input['email'], 'email')) {
                    throw new GeneralException(__('exceptions.frontend.auth.email_taken'));
                }

                // Force the user to re-verify his email address if config is set
                if (config('access.users.confirm_email')) {
                    $user->confirmation_code = md5(uniqid(mt_rand(), true));
                    $user->confirmed = false;
                    $user->notify(new UserNeedsConfirmation($user->confirmation_code));
                }
                $user->email = $input['email'];
                $updated = $user->save();

                // Send the new confirmation e-mail

                return [
                    'success' => $updated,
                    'email_changed' => true,
                ];
            }
        }

        return $user->save();
    }


    public function resizeAvatar($image)
    {

        $filename = $image->getPathName();

        // Set a maximum height and width
        $width = 200;
        $height = 200;

        // Content type


        // Get new dimensions
        list($width_orig, $height_orig) = getimagesize($image);

        $ratio_orig = $width_orig / $height_orig;

        if ($width / $height > $ratio_orig) {
            $width = $height * $ratio_orig;
        } else {
            $height = $width / $ratio_orig;
        }
        // Resample
        $image_p = imagecreatetruecolor($width, $height);

        if ($image->getMimeType() === "image/png") {
            $image = imagecreatefrompng($filename);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            imagepng($image_p, $filename, 9);


        } else {
            $image = imagecreatefromjpeg($filename);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            imagejpeg($image_p, $filename, 9);
        }

        return $filename;
    }

    /**
     * @param      $input
     * @param bool $expired
     *
     * @return bool
     * @throws GeneralException
     */
    public function updatePassword($input, $expired = false)
    {
        $user = $this->getById(auth()->id());

        if (Hash::check($input['old_password'], $user->password)) {
            if ($expired) {
                $user->password_changed_at = now()->toDateTimeString();
            }

            return $user->update(['password' => $input['password']]);
        }

        throw new GeneralException(__('exceptions.frontend.auth.password.change_mismatch'));
    }

    /**
     * @param $code
     *
     * @return bool
     * @throws GeneralException
     */
    public function confirm($code)
    {
        $user = $this->findByConfirmationCode($code);

        if ($user->confirmed === true) {
            throw new GeneralException(__('exceptions.frontend.auth.confirmation.already_confirmed'));
        }

        if ($user->confirmation_code === $code) {
            $user->confirmed = true;
            $user->api_token = Str::random(60);

            event(new UserConfirmed($user));

            return $user->save();
        }

        throw new GeneralException(__('exceptions.frontend.auth.confirmation.mismatch'));
    }

    /**
     * @param $code
     *
     * @return mixed
     * @throws GeneralException
     */
    public function findByConfirmationCode($code)
    {
        $user = $this->model
            ->where('confirmation_code', $code)
            ->first();

        if ($user instanceof $this->model) {
            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.not_found'));
    }

    /**
     * @param $data
     * @param $provider
     *
     * @return mixed
     * @throws GeneralException
     */
    public function findOrCreateProvider($data, $provider)
    {
        // User email may not provided.
        $user_email = $data->email ?: "{$data->id}@{$provider}.com";

        // Check to see if there is a user with this email first.
        $user = $this->getByColumn($user_email, 'email');

        /*
         * If the user does not exist create them
         * The true flag indicate that it is a social account
         * Which triggers the script to use some default values in the create method
         */
        if (!$user) {
            // Registration is not enabled
            if (!config('access.registration')) {
                throw new GeneralException(__('exceptions.frontend.auth.registration_disabled'));
            }

            // Get users first name and last name from their full name
            $nameParts = $this->getNameParts($data->getName());

            $user = parent::create([
                'first_name' => $nameParts['first_name'],
                'last_name' => $nameParts['last_name'],
                'email' => $user_email,
                'active' => true,
                'confirmed' => true,
                'password' => null,
                'avatar_type' => $provider,
            ]);

            if ($user) {
                // Add the default site role to the new user
                $user->assignRole(config('access.users.default_role'));
            }

            event(new UserProviderRegistered($user));
        }

        // See if the user has logged in with this social account before
        if (!$user->hasProvider($provider)) {
            // Gather the provider data for saving and associate it with the user
            $user->providers()->save(new SocialAccount([
                'provider' => $provider,
                'provider_id' => $data->id,
                'token' => $data->token,
                'avatar' => $data->avatar,
            ]));
        } else {
            // Update the users information, token and avatar can be updated.
            $user->providers()->update([
                'token' => $data->token,
                'avatar' => $data->avatar,
            ]);

            $user->avatar_type = $provider;
            $user->update();
        }

        // Return the user object
        return $user;
    }

    /**
     * @param $fullName
     *
     * @return array
     */
    protected function getNameParts($fullName)
    {
        $parts = array_values(array_filter(explode(' ', $fullName)));
        $size = count($parts);
        $result = [];

        if (empty($parts)) {
            $result['first_name'] = null;
            $result['last_name'] = null;
        }

        if (!empty($parts) && $size == 1) {
            $result['first_name'] = $parts[0];
            $result['last_name'] = null;
        }

        if (!empty($parts) && $size >= 2) {
            $result['first_name'] = $parts[0];
            $result['last_name'] = $parts[1];
        }

        return $result;
    }
}
