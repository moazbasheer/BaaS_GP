import { useEffect } from "react";

function Notification({ message, setMessage }) {
  useEffect(() => {
    setTimeout(() => setMessage(''), 4000)
  }, [message]);

  return ( 
    <div>
      {message}
    </div>
  )
}

export default Notification;