import { Link } from "react-router-dom";

function PathItem({ path, deletePath }) {
  return (
    <div>
      {path.path_name}
      <Link to={`../paths/${path.id}`}>
        <button>View</button>
      </Link>
      <button onClick={deletePath}>Delete</button>
    </div>
  )
}

export default PathItem;