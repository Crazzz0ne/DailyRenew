<template>
    <div>
        <p>Time Elapsed: {{ formattedTime }}</p>
    </div>
</template>

<script>
import dayjs from "dayjs";

export default {
    name: "TimeDisplay",
    props: {
        targetDate: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            timerText: "",
            timeElapsed: {
                hours: 0,
                minutes: 0,
                seconds: 0
            }
        };
    },
    mounted() {
        this.startTimer();
    },
    computed: {

        formattedTime() {
            let parts = [];

            if (this.timeElapsed.hours) {
                parts.push(`${this.timeElapsed.hours} hour${this.timeElapsed.hours > 1 ? 's' : ''}`);
            }

            if (this.timeElapsed.minutes) {
                parts.push(`${this.timeElapsed.minutes} minute${this.timeElapsed.minutes > 1 ? 's' : ''}`);
            }

            if (this.timeElapsed.seconds) {
                parts.push(`${this.timeElapsed.seconds} second${this.timeElapsed.seconds > 1 ? 's' : ''}`);
            }

            return parts.join(', ');
        }
    },
    methods: {
        startTimer() {
            setInterval(() => {
                const now = dayjs()
                const start = dayjs(this.targetDate)
                const diffMilliseconds = now.diff(start)

                // Convert the difference to hours, minutes, and seconds
                const diffSeconds = Math.floor(diffMilliseconds / 1000)
                const hours = Math.floor(diffSeconds / 3600)
                const minutes = Math.floor((diffSeconds % 3600) / 60)
                const seconds = diffSeconds % 60

                this.timeElapsed = {
                    hours,
                    minutes,
                    seconds
                }
            }, 1000)
        }
    },

    beforeDestroy() {
        clearInterval(this.startTimer);
    },
};
</script>
