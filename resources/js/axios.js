import axios from "axios";

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  withCredentials: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    "Accept": "application/json",
  },
});


//  CRSF token handling
//  on page load well request tht CSRF cookie if not already set
//  but axios automaticall read the XSRF-TOKEN cookie and set the X-XSRF-TOKEN header for us
//  Just call /sanctum/csrf-cookie endpoint before any state changing requests.


// interceptors to refresh CSRF token on 419 (expired) errors

apiClient.interceptors.response.use(
  response => response,
  async error => {
    if (error.response && error.response.status === 419) {
   
        // Request a new CSRF token
        await axios.get('/sanctum/csrf-cookie', {withCredentials: true});
        return apiClient.request(error.config); // Retry the original request
        }
        return Promise.reject(error);
    }
);

export default apiClient;
