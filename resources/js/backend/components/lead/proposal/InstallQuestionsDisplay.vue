<template>
    <div>
        <div class="text-center">
            <h4>Installer Questions</h4>
        </div>
        <div class="row justify-content-between pt2-5 border-bottom border-info">
            <div class="col-md-6 col-sm-12 py-3 col-xl-4 ">
                <label class=" my-auto">Has any unpermitted work been done to this home?</label>
            </div>
            <div class="col-md-6 col-sm-12 py-3 col-xl-4">
                <p class="text-uppercase">
                    {{ questions.un_permitted_work }}
                </p>
            </div>
        </div>
        <div class="row pt-3 justify-content-between  border-bottom border-info">
            <div class="col-md-6 col-sm-12 py-3 col-xl-4 ">
                <label class=" my-auto">Is tree removal part of this project?</label>
            </div>
            <div class="col-md-6 col-sm-12 py-3 col-xl-4">
                <div class="mx-auto">
                    <p class="text-uppercase">
                        {{ questions.tree_removal }}
                    </p>
                </div>
            </div>
        </div>
        <div class="row pt-3 justify-content-between  border-bottom border-info">
            <div class="col-md-6 col-sm-12 py-3 col-xl-4 ">
                <label class=" my-auto">Does the home have working Smoke/Carbon Monoxide Detectors?</label>
            </div>
            <div class="col-md-6 col-sm-12 py-3 col-xl-4">
                <div class="mx-auto">
                    <p class="text-uppercase">
                        {{ questions.alarms_working }}
                    </p>
                </div>
            </div>
        </div>
        <div class="row pt-3 justify-content-between  border-bottom border-info">
            <div class="col-md-6 col-sm-12 py-3 col-xl-4">
                <label class=" my-auto">Are there any access issues we should be aware of? </label>
            </div>
            <div class="col-md-6 col-sm-12 py-3 col-xl-4">
                <div class="mx-auto">
                    <p class="text-uppercase">
                        {{ questions.access_issues }}
                    </p>

                    <div class="pt-3">
                        <p class="text-uppercase">
                            {{ questions.access_issues_details }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pt-3 justify-content-between  border-bottom border-info">
            <div class="col-md-6 col-sm-12 py-3 col-xl-4">
                <label class=" my-auto">is there an HOA? </label>
            </div>
            <div class="col-md-6 col-sm-12 py-3 col-xl-4">
                <div class="mx-auto">
                    <p class="text-uppercase">
                        {{ questions.hoa }}
                    </p>
                </div>
            </div>
        </div>
        <div class="row pt-3 justify-content-between  border-bottom border-info " v-if="modelQuestions.hoa === 'yes'">
            <div class="col-md-6 col-sm-12 py-3 col-xl-4">
                <label class=" my-auto">HOA Name</label>
            </div>
            <div class="col-md-6 col-sm-12 py-3 col-xl-4">
                <div class="mx-auto">
                    <div
                         class="pt-3">
                        {{ modelQuestions.hoa_name}}

                    </div>

                </div>
            </div>
        </div>
        <div class="row pt-3 justify-content-between  border-bottom border-info " v-if="modelQuestions.hoa === 'yes'">
            <div class="col-md-6 col-sm-12 py-3 col-xl-4" >
                <label class=" my-auto">HOA contact</label>
            </div>
            <div class="col-md-6 col-sm-12 py-3 col-xl-4" >
                <div class="mx-auto">
                    <div
                        class="pt-3">
                        {{ modelQuestions.hoa_contact }}
                    </div>

                </div>
            </div>
        </div>
        <div class="row pt-3 justify-content-between  border-bottom border-info">
            <div class="col-md-6 col-sm-12 py-3 col-xl-4">
                <label class=" my-auto">Have you made any promises other than what is in the agreement?</label>
            </div>
            <div class="col-md-6 col-sm-12 py-3 col-xl-4">
                <div class="mx-auto">
                    <p class="text-uppercase">
                        {{ questions.promises }}
                    </p>
                    <div
                        v-if="questions.promises"
                        class="pt-3"
                    >
                        <p class="text-uppercase">
                            {{ questions.promises_details }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div/>
    </div>
</template>

<script>
export default {
    name: 'InstallQuestionsDisplay',
    props: ['questions']
}
</script>

<style scoped>

</style>
