import { useState, useEffect } from "react";
import { useParams, useNavigate  } from "react-router-dom";
import axios from "axios";
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

const EditCategory = () => {
  const { id } = useParams();
  const navigate = useNavigate();
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

  const [categoryData, setCategoryData] = useState({
    name: "",
    status: false,
  });

  useEffect(() => {
    axios
      .post(
        `http://localhost/phpchpldau/Controllers/Category.php?tag=getSingleCategory}`,
        { tag: "getSingleCategory", category_id: id },
        {
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
        }
      )
      .then((res) => {
        if (res.data.categoryList && res.data.categoryList.length > 0) {
          const category = res.data.categoryList[0];
          setCategoryData({
            name: category.name,
            status: category.status === "1",
          });
        }
      })
      .catch((err) => console.error(err));
  }, []);

  const handleFormSubmit = (e) => {
    e.preventDefault();

    axios
      .post(
        "http://localhost/phpchpldau/Controllers/Category.php",
        {
          tag: "UpdateCategory",
          category_id: id,
          category_name: categoryData.name,
          category_status: categoryData.status,
        },
        {
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
        }
      )
      .then((res) => {
        notifys(res.data.message);
        navigate("/"); 
      })
      .catch((err) => {
        notifye("An error occurred:", err);
      });
  };

  return (
    <div className="max-w-md mx-auto mt-5 p-6 bg-white shadow-md">
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
      <h2 className="text-xl font-semibold mb-4">Edit Category</h2>
      <form onSubmit={handleFormSubmit}>
        <div className="mb-4">
          <label htmlFor="name" className="block text-gray-700">
            Category Name
          </label>
          <input
            type="text"
            id="name"
            name="name"
            className="form-input mt-1 block w-full"
            value={categoryData.name}
            onChange={(e) =>
              setCategoryData({ ...categoryData, name: e.target.value })
            }
          />
        </div>
        <div className="mb-4">
          <label htmlFor="status" className="block text-gray-700">
            Status
          </label>
            <input
            type="checkbox"
            id="status"
            name="status"
            className="form-checkbox mt-1"
            checked={categoryData.status == "true"}
            onChange={() =>
            setCategoryData({
                ...categoryData,
                status: categoryData.status == "true" ? "false" : "true",
            })
            }
            />


        </div>
        <div className="mt-6">
          <button
            type="submit"
            className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Save
          </button>
        </div>
      </form>
    </div>
  );
};

export default EditCategory;
