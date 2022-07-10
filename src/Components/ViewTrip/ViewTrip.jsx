import { useParams } from "react-router-dom";

function ViewTrip() {
  const tripId = useParams().id

  return (
    <div>Hello World!</div>        
  )
}

export default ViewTrip;