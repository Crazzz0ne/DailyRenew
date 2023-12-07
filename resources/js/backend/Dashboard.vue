<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-header">
            <!--                        <h3>@lang('strings.backend.dashboard.welcome') {{ $logged_in_user->name }}!</h3>-->
          </div><!--card-header-->
          <div class="card-body">
            <div class="container">
              <div class="row text-center justify-content-center">
                <div class="col-xs-6 pb-3 mx-auto">
                  <a
                    href="/dashboard/lead"
                    class="btn dash-btn btn-danger text-white"
                  >
                    <i class="nav-icon fas fa-house-damage fa-4x" />
                    <div class="pt-2"><b>Lead</b></div>
                  </a>
                </div>
                <div class="col-xs-6 pb-3 mx-auto">
                  <div>
                    <a
                      href="/dashboard/calendar"
                      class="btn bg-yellow text-white"
                    >
                      <i
                        class="fas fa-calendar fa-4x px-3 px-1"
                        aria-hidden="true"
                      />
                      <div class="pt-2"><b>Calendar</b></div>
                    </a>
                  </div>
                </div>
                <div class="col-xs-6 pb-3 mx-auto">
                  <div>
                    <a
                      href="/dashboard/lead/queue"
                      class="btn bg-indigo text-white"
                    >
                      <i
                        class="fas fa-tasks fa-4x px-3 px-1"
                        aria-hidden="true"
                      />
                      <div class="pt-2"><b>Queue</b></div>
                    </a>
                  </div>
                </div>
                <div class="col-xs-6 pb-3 mx-auto">
                  <a
                    href="https://docs.google.com/forms/d/e/1FAIpQLSd6ZZDHmWc3GLJe5c6ZzbrxAA_Au7tB4EaR3jndBB2Uf4CkZg/viewform?usp=sf_link"
                    class="btn dash-btn bg-behance text-white"
                    target="_blank"
                  >
                    <i class="fas fa-comments-dollar fa-4x px-3 px-1" />
                    <div class="pt-2"><b>Payroll Items</b></div>
                  </a>
                </div>
                <div class="col-xs-6 pb-3 mx-auto">
                  <div>
                    <a
                      href="/dashboard/training"
                      class="btn btn-third"
                    >
                      <i
                        class="fas fa-graduation-cap fa-4x px-3 px-1"
                        aria-hidden="true"
                      />
                      <div class="pt-2"><b>Training</b></div>
                    </a>
                  </div>
                </div>
                <!--                                <div class="col-xs-6 pb-3 mx-auto">-->
                <!--                                    <div>-->
                <!--                                        <a href="/dashboard/printable.all"-->
                <!--                                           class="btn text-white bg-gray-500">-->
                <!--                                            <i class="fas fas fa-print fa-4x px-3 px-1" aria-hidden="true"></i>-->
                <!--                                            <div class="pt-2"><b>Printable</b></div>-->
                <!--                                        </a>-->
                <!--                                    </div>-->
                <!--                                </div>-->

                <div class="col-xs-6 pb-3 mx-auto">
                  <div>
                    <a
                      href="/dashboard/commission"
                      class="btn bg-green text-white"
                    >
                      <i
                        class="fas fa-money-bill fa-4x px-3 px-1"
                        aria-hidden="true"
                      />
                      <div class="pt-2"><b>Commissions</b></div>
                    </a>
                  </div>
                </div>

                <div class="col-xs-6 pb-3 mx-auto">
                  <div>
                    <a
                      href="/dashboard/report"
                      class="btn bg-cyan text-white"
                    >
                      <i
                        class="fas fa-file fa-4x px-3 px-1"
                        aria-hidden="true"
                      />
                      <div class="pt-2"><b>Reports</b></div>
                    </a>
                  </div>
                </div>

                <!--                                <div class="col-xs-6 pb-3 mx-auto">-->
                <!--                                    <div>-->
                <!--                                        <a href="dashboard.roundRobin') }}"-->
                <!--                                           class="btn bg-spotify text-white">-->
                <!--                                            <i class="fas fa-kiwi-bird fa-4x px-3 px-1" aria-hidden="true"></i>-->
                <!--                                            <div class="pt-2"><b>Round Robin</b></div>-->
                <!--                                        </a>-->
                <!--                                    </div>-->
                <!--                                </div>-->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
	name: 'Dashboard',
	data () {
		return {}
	},
	created () {
		this.fetchUsers()
	},
	methods: {
		...mapActions(['fetchUsers'])
	},
	computed: {
		...mapGetters(['getUser'])
	}
}
</script>

<style scoped>

</style>
