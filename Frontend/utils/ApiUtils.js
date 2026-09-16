import axios from "axios";
import env from "../configs/env.js";

export async function fetchApi({
    method = "GET",
    url = "",
    data = null,
    params = null,
    headers = {},
    baseURL = env.backend_url,
    contentType = "application/json",
    responseType = "json",
}) {
    const token = localStorage.getItem("token");

    // Siapkan headers
    const configHeaders = {
        ...headers,
        ...(token && { Authorization: `Bearer ${token}` }),
        "Content-Type": contentType,
    };

    // Siapkan konfigurasi Axios
    const config = {
        method,
        url,
        baseURL,
        headers: configHeaders,
        responseType,
        ...(params && { params }),
        ...(data && { data }),
    };

    try {
        const response = await axios(config);
        return response.data;
    } catch (error) {
        const status = error.response?.status;
        
        if (!status) {
            // Gagal mendapatkan response dari Backend
            alert('Error on fetching API.')
        } else {
            alert(error?.response?.data?.message || 'Error on fetching API.')
        }
        console.error(error);

        throw error;
    }
}