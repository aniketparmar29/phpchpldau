import axios from "axios";
import { useEffect, useState } from "react";
import {AiFillDelete} from 'react-icons/ai'
import {TbEdit} from 'react-icons/tb'
import PropTypes from "prop-types";
import {CgEditBlackPoint} from 'react-icons/cg'
const DisplayCategory = ({op, notifys,notifye,setOp}) => {
    const [Data, setData] = useState([]);

    const handleDelteCategory = async (id) => {
      try {
        const formData = {
          tag: "DeleteCateogory",
          category_id: id,
        };
        const response = await axios.post(
          "http://localhost/phpchpldau/Controllers/Category.php",
          formData,
          {
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
          }
        );
  
        if (response.data.status == 200) {
            setOp(prevOp => !prevOp);
          notifys(response.data.message);
        } else {
          notifye(response.data.message);
        }
      } catch (error) {
        notifye("An error occurred:", error);
      }
    };
  
    useEffect(() => {
      const formData = {
        tag: "getCategory",
      };
  
      axios
        .post("http://localhost/phpchpldau/Controllers/Category.php", formData, {
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
        })
        .then((res) => {
          setData(res.data.categoryList);
        })
        .catch((err) => console.error(err));
    }, [op]);
  

  


  return (
    <div>
    <div className="overflow-x-auto z-0  ">
        <table className="w-[80%] m-auto text-lg text-left text-blue-100 dark:text-blue-100 shadow-md sm:rounded-lg">
            <thead className="text-xs text-white uppercase bg-blue-600 border-b border-blue-400 dark:text-white">
                <tr>
                    <th scope="col" className="px-6 py-3">
                        Category name
                    </th>
                    <th scope="col" className="px-6 py-3">
                        Status
                    </th>
                    <th scope="col" className="px-6 py-3">
                        Edit
                    </th>
                    <th scope="col" className="px-6 py-3">
                        Delete
                    </th>
                </tr>
            </thead>
            <tbody>
            {Data.length > 0 &&
        Data.map((el, index) => (
          <tr
            key={index}
            className="bg-blue-200 border-b border-blue-400 hover:bg-blue-300"
          >
            <th
              scope="row"
              className="px-6 py-4 font-medium text-blue-500 whitespace-nowrap dark:text-blue-600"
            >
              {el.name}
            </th>
            <td className="px-9 py-4">
              {el.status === "true" ? (
                <CgEditBlackPoint className="text-green-600" />
              ) : (
                <CgEditBlackPoint className="text-red-600" />
              )}
            </td>
            <td className="px-8 py-4">
              <a
                 
                className="font-medium text-blue-600 hover:underline"
              >
                <TbEdit  />
              </a>
            </td>
            <td className="px-9 py-4">
              <a href="#" onClick={() => handleDelteCategory(el.category_id)} className="font-medium text-blue-600 hover:underline">
                <AiFillDelete />
              </a>
            </td>
          </tr>
        ))}
            </tbody>
        </table>
    </div>
    </div>
  )
}

export default DisplayCategory
DisplayCategory.propTypes = {
    op: PropTypes.bool.isRequired,   
    setOp: PropTypes.func.isRequired,   
    notifys: PropTypes.func.isRequired,   
    notifye: PropTypes.func.isRequired,   
  };