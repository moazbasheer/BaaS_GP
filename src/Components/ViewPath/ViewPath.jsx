import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import ViewPathMap from "../ViewPathMap/ViewPathMap"
import pathService from '../../Services/paths';

function ViewPath() {
  const pathId = parseInt(useParams().id)
  const [path, setPath] = useState()

  useEffect(() => {
    pathService.get(pathId).then((result) => {
      setPath(result.message);
    });
  }, [])

  return (
    <>
      <ViewPathMap path={path}/>
    </>
  );
}

export default ViewPath;
