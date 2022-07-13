import { Link } from 'react-router-dom';
import DeleteOutlineOutlinedIcon from '@mui/icons-material/DeleteOutlineOutlined';

function PathItem({ path, deletePath }) {
  return (
    <div className='d-flex justify-content-between align-items-center'>
      <div>
        {path.path_name}
      </div>
      <div>
        <Link className="text-center fst-italic mx-2" to={`../paths/${path.id}`}>
          View Path
        </Link>
        <button className='btn btn-danger' onClick={deletePath}><DeleteOutlineOutlinedIcon />Delete</button>
      </div>
    </div>
  );
}

export default PathItem;
