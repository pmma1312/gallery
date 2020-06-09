import navbar from './components/navigation.js';

const app = new Vue({
    el: "#root",
    data: {
        limit: 30,
        offset: 0,
        albums: []
    },
    components: {
        navbar
    },
    methods: {
        loadAlbums() {
            axios.get(`/api/albums/${this.limit}/${this.offset}`)
            .then(response => {
                if(response.data.data.length > 0) {
                    response.data.data.forEach(element => {
                        this.albums.push(element);
                    });
                }
            })
            .catch(error => {
                if(error.response) {
                    Swal.fire(
                        "Error!",
                        error.response.data.message,
                        "error"
                    );
                }
            });
        }
    },
    mounted() {
        this.loadAlbums();
    }
});