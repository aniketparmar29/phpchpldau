import  { useEffect, useState } from "react";
import AOS from "aos";
import axios from "axios"; 
import PropTypes from "prop-types";

const AddCategory = ({setOp,op,notifye,notifys,toggleadct}) => {
  const [CatName, setCatName] = useState("");
  const [CatStatus, setCatStatus] = useState("");
  useEffect(() => {
    AOS.init({
      duration: 3000,
    });
  }, []);

  const handleAddCategory = async () => {
    try {
      const formData = {
        tag:"AddCategory",
        category_name: CatName,
        category_status: CatStatus,
      };
      const response = await axios.post(
        "https://prepapi.dgk-organic-urjavan-foods.co.in/Controllers/Category.php",
        formData,
        {
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
        }
      );
      
        if(response.data.status==200){
          toggleadct();
          notifys(response.data.message)
        }else{
          notifye(response.data.message)
        }


     setOp(!op);
    } catch (error) {
      notifye("An error occurred:", error);
    }
  };

  return (
    <div
      data-aos="fade-down"
      className="absolute p-6 mx-auto right-[35%] w-[35%] h-[45%]  border rounded-lg shadow-lg bg-white z-50 mb-5"
    >
      <h2 className="text-2xl font-semibold mb-4">Add Category</h2>

      <div className="mb-4">
        <label
          htmlFor="name"
          className="block text-gray-700 text-sm font-medium mb-2"
        >
          Category Name
        </label>
        <input
          type="text"
          id="name"
          name="name"
          className="w-full border rounded-lg py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-400"
          placeholder="Enter Category Name"
          value={CatName}
          onChange={(e) => setCatName(e.target.value)}
        />
      </div>
      <div className="mb-4">
        <label
          htmlFor="status"
          className="block text-gray-700 text-sm font-medium mb-2"
        >
          Status
        </label>
        <div className="flex items-center">
          <input
            type="checkbox"
            id="status"
            name="status"
            className="form-checkbox h-5 w-5 text-blue-600"
            checked={CatStatus}
            onChange={(e) => setCatStatus(e.target.checked)}
          />
          <span className="ml-2 text-gray-700 text-sm">True / False</span>
        </div>
      </div>
      <div className="text-center">
        <button
          onClick={handleAddCategory}
          className="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-lg focus:outline-none"
        >
          Add
        </button>
      </div>
    </div>
  );
};

export default AddCategory;
AddCategory.propTypes = {
  setOp: PropTypes.func.isRequired,
  notifye: PropTypes.func.isRequired,
  notifys: PropTypes.func.isRequired,
  toggleadct: PropTypes.func.isRequired,
  op: PropTypes.bool.isRequired,   
};