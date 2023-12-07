<template>
    <GmapMap
        v-if="avaragedCenter"
        :center="avaragedCenter"
        :zoom="12"
        style="width:100%; height: 400px;"

    >
        <GmapMarker
            v-for="(location, index) in formattedAddresses"
            :key="index"
            :position="location.position"
            @click="showInfoWindow(index)"
        >{{location.label}}</GmapMarker>
        <GmapInfoWindow
            :options="infoOptions"
            :position="infoWindowPosition"
            :opened="infoWindowOpened"
            @closeclick="infoWindowOpened=false"
        >
            <a :href="infoWindowContent.link" target="_blank">
                {{ infoWindowContent.label }}
            </a>
        </GmapInfoWindow>
    </GmapMap>
</template>

<script>
import { gmapApi } from "vue2-google-maps";

export default {
    name: 'GeoMap',
    props: {
        addresses: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            center: { lat: 35.7864595625
                , lng: -119.23084875 },

            infoWindowPosition: {},
            infoWindowContent: {},
            infoWindowOpened: false,
            infoOptions: {
                pixelOffset: {
                    width: 0,
                    height: -35
                }
            }
        };
    },
    mounted() {
        // this.center = this.addresses.reduce((acc, address) => {
        //     return {
        //         lat: acc.lat + address.location.long,
        //         lng: acc.lng + address.location.lat
        //     }
        // }, { lat: 0, lng: 0 });

        this.getUserLocation();
    },
    watch: {
        // addresses() {
        //     this.center = this.addresses.reduce((acc, address) => {
        //         return {
        //             lat: acc.lat + address.location.long,
        //             lng: acc.lng + address.location.lat
        //         }
        //     }, { lat: 0, lng: 0 });
        // }
    },
    methods: {


        showInfoWindow(index) {
            this.infoWindowPosition = this.formattedAddresses[index].position;
            this.infoWindowContent = this.formattedAddresses[index];
            this.infoWindowOpened = true;
        },
    //     get users latitude and longitude and set it to center
        getUserLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    this.center = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                });
            }
        },
        tagsToString(tags) {
            return tags.map(tag => tag.name.en).join(', ');
        }
    },
    computed: {
        google: gmapApi,
        formattedAddresses() {
            return this.addresses.map(address => {
                return {
                    position: {
                        lat: address.location.long,
                        lng: address.location.lat
                    },
                    // tags array to string tags[].name
                    tags: address.tags,



                    label: `${address.name}  ${this.tagsToString(address.tags)}`,
                    link: `https://www.google.com/maps/place/${address.street_name.replace(/\s+/g, '+')},${address.city.replace(/\s+/g, '+')},${address.state}+${address.zip}`
                }
            });
        },
        avaragedCenter() {

            let totalAddresses = this.addresses.length;
            if (totalAddresses === 0) {
                return null;
            }

            let addresses = this.formattedAddresses.reduce((acc, address) => {
                return {
                    lat: acc.lat + address.position.lat,
                    lng: acc.lng + address.position.lng
                };
            }, { lat: 0, lng: 0 });

            if (totalAddresses > 0) {
                addresses.lat = addresses.lat / totalAddresses;
                addresses.lng = addresses.lng / totalAddresses;
            }
            this.$emit('center', addresses);
            return addresses;
            return this.addresses.reduce((acc, address) => {
                return {
                    lat: acc.lat + address.location.long,
                    lng: acc.lng + address.location.lat
                }
            }, { lat: 0, lng: 0 });
        },
    }
};
</script>

