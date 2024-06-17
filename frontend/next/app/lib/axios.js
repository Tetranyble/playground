import axios from 'axios';
import Cookies from 'js-cookie'; // Assuming you use js-cookie for handling cookies

// Create an Axios instance
const api = axios.create({
  baseURL: 'https://api.ugbanawaji.com/api/v1/', // Use the environment variable
  withCredentials: true, // This ensures cookies are sent with requests
  headers:{
    Accept: 'application/json'
  }
});

// Function to refresh the access token
const refreshToken = async () => {
  try {
    const response = await axios.post(`${'https://api.ugbanawaji.com/api/v1/'}/auth/refresh-token`, {
      refreshToken: Cookies.get('refreshToken'), // Get the refresh token from cookies
    });

    // Store the new access token in cookies
    Cookies.set('accessToken', response.data.accessToken, { expires: 1 / 24 }); // Expires in 1 hour, adjust as needed

    return response.data.accessToken;
  } catch (error) {
    console.error('Failed to refresh token', error);
    throw error;
  }
};

// Request interceptor to add the access token to headers
api.interceptors.request.use(
  config => {
    const token = Cookies.get('accessToken');
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle token expiration
api.interceptors.response.use(
  response => {
    return response;
  },
  async error => {
    const originalRequest = error.config;

    if (error.response.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;

      try {
        const newAccessToken = await refreshToken();

        // Update the original request with the new token
        originalRequest.headers['Authorization'] = `Bearer ${newAccessToken}`;

        // Retry the original request
        return api(originalRequest);
      } catch (refreshError) {
        console.error('Token refresh failed', refreshError);
        // Optionally, handle logout or redirect to login page
      }
    }

    return Promise.reject(error);
  }
);

export default api;
