<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;

/**
 * Class Handler.
 */
class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		GeneralException::class,
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = [
		'password',
		'password_confirmation',
	];

	/**
	 * Report or log an exception.
	 *
	 * @param Throwable $exception
	 *
	 * @return mixed|void
	 * @throws Throwable
	 */
	public function report(Throwable $exception)
	{
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param Request $request
	 * @param Throwable $exception
	 * @return Response
	 */
	public function render($request, Throwable $exception)
	{
		if ($exception instanceof UnauthorizedException) {
			return redirect()
				->route(home_route())
				->withFlashDanger(__('auth.general_error'));
		}

		return parent::render($request, $exception);
	}

	/**
	 * @param Request $request
	 * @param AuthenticationException $exception
	 *
	 * @return JsonResponse|RedirectResponse
	 */
	protected function unauthenticated($request, AuthenticationException $exception)
	{
		return $request->expectsJson()
			? response()->json(['message' => 'Unauthenticated.'], 401)
			: redirect()->guest(route('frontend.auth.login'));
	}
}
