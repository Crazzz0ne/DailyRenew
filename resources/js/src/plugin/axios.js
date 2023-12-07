// /src/plugins/axios.js
import axios from 'axios'

axios.interceptors.request.use(
	(request) => {
		request.headers = {
			Authorization: `Bearer ${window.apiKey}`,
			'Content-Type': 'application/json',
			Accept: 'application/json'
		}
		// console.log('in it', window.apiKey)
		// do something with request meta data, configuration, etc

		// dont forget to return request object,
		// otherwise your app will get no answer
		return request
	}
)

// doing something with the response
axios.interceptors.response.use(
	(response) => {
		// all 2xx/3xx responses will end here

		return response
	},
	(error) => {
		// Basically just cancelling the error popup if the requested page can't be found.
		// TODO: Include a check for /notification in routes only?
		if (error.response.status === 404) { return null }

		if (error.response.status === 422) {
			return Promise.reject(error)
		}

		// all 4xx/5xx responses will end here
		Swal.fire({
			type: 'error',
			title: 'Oops...',
			text: 'Something went wrong!',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Submit an issue?',
			cancelButtonText: 'No Thanks'
		}).then((result) => {
			if (result.value) {
				document.getElementById('submitIssueButton').click()
			}
		})
		return Promise.reject(error)
	}
)

export default axios
