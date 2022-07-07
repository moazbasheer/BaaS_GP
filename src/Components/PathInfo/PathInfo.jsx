function PathInfo(props) {
  const getTimeString = time => {
    const seconds = time / 1000
    const hours = Math.floor(seconds / 3600)
    const minutes = Math.floor((seconds - hours * 3600) / 60)
  
    const list = []
    if (hours > 0) {
      list.push(`${hours} hour(s)`)
    }
  
    if (minutes > 0) {
      list.push(`${minutes} minute(s)`)
    }
  
    if (list.length > 0) {
      return list.join(', ')
    }
    else {
      return '<1 minute'
    }
  }
  
  const getDistanceString = distance => {
    return (distance / 1000).toFixed(2) + ' km'
  }

  return (
    <div>
      <p>Estimated Time: {props.time ? getTimeString(props.time) : '--'}</p>
      <p>Distance: {props.distance ? getDistanceString(props.distance) : '--'}</p>
    </div>
  );
}

export default PathInfo;