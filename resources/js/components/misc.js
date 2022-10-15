export const getData = (url, headers = {}) => {
	return fetch(url, {headers: headers})
		.then((response) => response.json())
		.then((data) => data);
};

export const postData = async (url = "", data = {}, headers = {}) => {
	// Default options are marked with *
	const response = await fetch(url, {
		method: "POST", 
		headers: {
			"Content-Type": "application/json",
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		body: JSON.stringify(data), // body data type must match "Content-Type" header
	});
	return response.json(); // parses JSON response into native JavaScript objects
};