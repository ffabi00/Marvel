<script>
import axios from 'axios';

const ApiService = {
    async fetchData (apiUrl, params = {}) {
        try {
            const response = await axios.get(apiUrl, { params });
            return response.data;
        } catch (error) {
            console.error('Erro ao carregar os dados:', error);
            throw error;
        }
    }
};

export default ApiService;
</script>