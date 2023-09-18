import { useState } from "react"
import AddCategory from "../Components/AddCategory"
import DisplayCategory from "../Components/DisplayCategory"
import {CgCloseO} from 'react-icons/cg'
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

const Home = () => {
    const [Toggleval, setToggleval] = useState(false)
    const [op, setOp] = useState(false)
    const toggleadct = () =>{
        setToggleval(!Toggleval);
    }
    const notifys = (msg) => toast.success(msg, {
        position: "top-center",
        autoClose: 5000,
        hideProgressBar: false,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
        theme: "colored",
        });
    const notifye = (msg) => toast.error(msg, {
        position: "top-center",
        autoClose: 5000,
        hideProgressBar: false,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
        theme: "colored",
        });
  return (
    <div>
        <button className="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 m-5 rounded-lg shadow-lg focus:outline-none"  onClick={toggleadct}>{Toggleval ? <CgCloseO/> : "Add Category"}</button>
           {Toggleval && <AddCategory setOp={setOp} op={op} toggleadct={toggleadct} notifys={notifys}  notifye={notifye}/>}
         <DisplayCategory notifys={notifys} notifye={notifye} op={op} setOp={setOp}/>  
        <ToastContainer
        position="top-right"
        autoClose={5000}
        hideProgressBar={false}
        newestOnTop={false}
        closeOnClick
        rtl={false}
        pauseOnFocusLoss
        draggable
        pauseOnHover
        theme="colored"
        />
    </div>
  )
}

export default Home