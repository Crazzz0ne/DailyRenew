import axios from 'axios'

export default axios.create({
	baseURL: '/',
	headers: {
		Accept: 'application/json',
		'Content-Type': 'application/json'
	}
})
