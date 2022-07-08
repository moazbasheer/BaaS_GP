import { useState } from "react";

function CreateTrip() {
	const [tripForm, setTripForm] = useState({})

	const handleSubmit = event => {
		console.log(event)
	}

	const handleChange = key => event => {
    console.log(key)
  }

	return (
		<>
			<form onSubmit={handleSubmit}>
        <input value={tripForm.name} onChange={handleChange('name')} />
			</form>
		</>
	)
}

export default CreateTrip