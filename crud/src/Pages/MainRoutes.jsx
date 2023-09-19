import { Route,Routes } from "react-router-dom"
import Home from "./Home"
import EditCategory from "./EditCategory"
const MainRoutes = () => {
  return (
    <>
    <Routes>
        <Route path="/" element={<Home/>} />
        <Route path="edit/:id" element={<EditCategory/>} />
    </Routes>
    </>
  )
}

export default MainRoutes