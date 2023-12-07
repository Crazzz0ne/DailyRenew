import * as axios from 'axios'

function upload (formData, leadId) {
	const url = `/api/salesflow/lead/${leadId}/upload`

	return axios.post(url, formData)
	// get data
		.then(x => console.log('yee', x))
	// add url field
		.then(x => x.map(img => Object.assign({},
			img, { url: `${BASE_URL}/images/${img.id}` })))
}

export { upload }
