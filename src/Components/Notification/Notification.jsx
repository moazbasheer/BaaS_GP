function Notification({ children }) {
  if (!children) {
    return null
  }

  return (
    <>
      <div>
        {children}
      </div>
    </>
  )
}

export default Notification;