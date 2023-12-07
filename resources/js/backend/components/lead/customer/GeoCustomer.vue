<script>
import 'vue-slider-component/theme/antd.css'
import CustomSlider from "../CustomSlider.vue";
import GeoMap from "./GeoMap.vue";
import AutoComplete from "../location/AutoComplete.vue";

const haversineDistance = (lat1, lon1, lat2, lon2) => {
    const R = 6371;
    const dLat = (lat2 - lat1) * (Math.PI / 180);
    const dLon = (lon2 - lon1) * (Math.PI / 180);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
};
export default {
    name: "GeoCustomer",
    components: {AutoComplete, GeoMap, CustomSlider},

    mounted() {
        this.getAddress();
    },
    data() {
        return {
            hasDialogOpenFullSize: false,
            radius: 500,
            addresses: [],
            loading: false,
            center: {
                lat: 35.7864595625,
                lng: -119.23084875
            },
            travelToAllOption: false,
            startAddress: null,
            showAutoComplete: false,
            selectedFilter: null,
            filterOptions: [
                {
                    label: 'All',
                    value: 'null'
                },
                {
                    label: 'Closed',
                    value: 'closed'
                },
                {
                    label: 'Credit Pass Not Closed',
                    value: 'credit-pass-not-closed'
                },
                {
                    label: 'Sat Not Closed',
                    value: 'sat-not-closed'
                },
                {
                    label: 'PTO',
                    value: 'pto'
                }
            ]
        }
    },
    methods: {
        center() {
           console.log('test')
        },
        openTravelToAll(url) {
            // open new Window
            window.open(url, '_blank');
        },
        getAddress() {
            this.loading = true;
            const payload = {
                radius: this.radius,
                filter: this.selectedFilter,

            };
            axios.post(`/api/customer/${this.$route.params.customerId}/geo`, payload)
                .then(response => {
                    this.addresses = response.data.data.data;
                    this.loading = false;
                })
                .catch(error => {
                    this.loading = false;
                });

        },

        roundNumber(number) {
            return number.toFixed(2);
        },
        setStartAddress(place) {
            console.log(place);
            this.startAddress = place;
            this.showAutoComplete = false;
            this.travelToAllOption = true;
        },
        changeFilter() {
            console.log(this.selectedFilter);
            this.getAddress();
        }
    },
    computed: {
        sorted() {
            //     sort addresses by distance
            return this.addresses.sort((a, b) => {
                return a.distance - b.distance;
            });
        },
        closestAndSortedPoints() {
            if (!this.startAddress) {
                return {
                    closest: null,
                    sortedPoints: []
                };
            }
            const pointsCopy = [...this.addresses];
            let closest = pointsCopy[0];
            let minDist = haversineDistance(this.startAddress.lat, this.startAddress.long, closest.location.lat, closest.location.long);
            let sortedPoints = [];

            // Find closest point to center
            for (const point of pointsCopy) {
                const dist = haversineDistance(this.startAddress.lat, this.startAddress.long, point.location.lat, point.location.long);
                if (dist < minDist) {
                    closest = point;
                    minDist = dist;
                }
            }

            // Remove closest point from array
            const index = pointsCopy.findIndex(point => point.id === closest.id);
            pointsCopy.splice(index, 1);
            sortedPoints.push(closest);

            // Sort remaining points using nearest neighbor algorithm
            let currentPoint = closest;

            while (pointsCopy.length > 0) {
                let nearestPoint = pointsCopy[0];
                let nearestDistance = haversineDistance(
                    currentPoint.location.lat,
                    currentPoint.location.long,
                    nearestPoint.location.lat,
                    nearestPoint.location.long
                );

                for (const point of pointsCopy) {
                    const dist = haversineDistance(
                        currentPoint.location.lat,
                        currentPoint.location.long,
                        point.location.lat,
                        point.location.long
                    );

                    if (dist < nearestDistance) {
                        nearestPoint = point;
                        nearestDistance = dist;
                    }
                }

                sortedPoints.push(nearestPoint);
                const nearestIndex = pointsCopy.findIndex(point => point.id === nearestPoint.id);
                pointsCopy.splice(nearestIndex, 1);
                currentPoint = nearestPoint;
            }

            return {
                closest,
                sortedPoints
            };
        },
        googleMapsUrls() {
            const baseUrl = 'https://www.google.com/maps/dir/';
            const urlSuffix = '?entry=ttu';
            if (!this.startAddress) {
                return [];
            }

            const startAddress = encodeURIComponent(`${this.startAddress.street_address}, ${this.startAddress.city}, ${this.startAddress.state}, ${this.startAddress.zip}`);
            const closestPoint = this.closestAndSortedPoints.closest;
            const endAddress = encodeURIComponent(`${closestPoint.street_name}, ${closestPoint.city}, ${closestPoint.state}, ${closestPoint.zip}`);

            let sortedAddresses = this.closestAndSortedPoints.sortedPoints.map(point => {
                return encodeURIComponent(`${point.street_name}, ${point.city}, ${point.state}, ${point.zip}`);
            });

            const urls = [];
            let currentUrl = `${baseUrl}${startAddress}/`;
            let addressCount = 0;

            sortedAddresses.forEach(address => {
                const tempUrl = `${currentUrl}${address}/${endAddress}${urlSuffix}`;

                if (tempUrl.length > 2000 || addressCount >= 20) {
                    urls.push(currentUrl.slice(0, -1) + urlSuffix);
                    currentUrl = `${baseUrl}${startAddress}/${address}/`;
                    addressCount = 1; // Reset the address count
                } else {
                    currentUrl += `${address}/`;
                    addressCount++;
                }
            });

            currentUrl += `${endAddress}${urlSuffix}`;
            urls.push(currentUrl);

            return urls;
        }
    },
}
</script>

<template>
    <div>

        <div class="container">
            <!--                back button-->
            <div class="row">
                <div class="col-md-12">
                    <MazBtn @click="$router.go(-1)">Back</MazBtn>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h2>Logistics</h2>
                </div>
                <div class="card-body">
                    <div class="row" v-if="travelToAllOption" style="min-height: 150px">
                        <!-- other elements -->
                        <
                        <div class="col-md-3 col-sm-6" v-for="(url, index) in googleMapsUrls" :key="index">
                            <MazBtn @click="openTravelToAll(url)">Get Directions on Google Maps - Part {{
                                    index + 1
                                }}
                            </MazBtn>
                        </div>
                        <p>Total Waypoints: {{ closestAndSortedPoints.sortedPoints.length }}</p>
                    </div>
                    <div v-else style="min-height: 150px">
                        <MazBtn v-if="!showAutoComplete" @click="showAutoComplete = true">Get Directions on Google
                            Maps
                        </MazBtn>
                        <AutoComplete
                            style=" z-index: 1000 !important;"
                            v-if="showAutoComplete"
                            @place="setStartAddress"/>

                    </div>

                    Choose a Radius (Meters) <strong> {{ radius }}</strong>
                    <CustomSlider
                        v-model="radius"
                        :value="1000"
                        :max="5000"
                        @input="getAddress()"
                    />
                    <GeoMap class="py-5" v-if="sorted.length" :addresses="sorted" @center="center">

                    </GeoMap>

                    <div class="row justify-content-between py-3">
                        <select v-model="selectedFilter" @change="getAddress">
                            <option v-for="filter in filterOptions" :value="filter.value">{{ filter.label }}</option>
                        </select>
                    </div>
                    <div class="row" v-if="!loading">
                        <div class="col-md-3 col-sm-12 mb-4" v-for="address in sorted" :key="address.name">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">{{ address.name }}</h5>
                                    <p class="card-text">{{ address.street_name }}, {{ address.city }} </p>
                                    <p class="card-text">Distance: {{ roundNumber(address.distance) }} M </p>

                                    <div class="d-flex flex-wrap mb-2">
                                    <span class="badge badge-primary m-1" v-for="tag in address.tags"
                                          :key="tag.name.en">
                                      {{ tag.name.en }}
                                    </span>
                                    </div>

                                    <a :href="`https://www.google.com/maps/place/${address.street_name.replace(/\s+/g, '+')},${address.city.replace(/\s+/g, '+')},${address.state}+${address.zip}`"
                                       target="_blank" class="btn btn-primary">
                                        View on Map
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        Loading
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
.tag-card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.tag-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}
</style>
