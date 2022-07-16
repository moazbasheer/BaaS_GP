import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import ViewPathMap from '../ViewPathMap/ViewPathMap';
import pathService from '../../Services/paths';
import PageTitle from '../PageTitle/PageTitle';

function ViewPath() {
  const pathId = parseInt(useParams().id, 10);
  const [path, setPath] = useState();

  useEffect(() => {
    pathService.get(pathId).then((result) => {
      setPath(result.message);
    });
  }, []);

  return (
    <>
      {path && <PageTitle title={`Viewing '${path.path_name}' Map.`} />}
      <ViewPathMap path={path} />
    </>
  );
}

export default ViewPath;
