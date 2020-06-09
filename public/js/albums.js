import navbar from './components/navigation.js';

const app = new Vue({
    el: "#root",
    data: {
        limit: 30,
        offset: 0,
        albums: [
            {
                name: "test",
                created_at: "09.06.2020",
                username: "testUser",
                thumbnail: "/public/img/uploads/example.jpg"
            },
            {
                name: "test2",
                created_at: "09.06.2020",
                username: "testUser",
                thumbnail: "/public/img/uploads/example.jpg"
            },
            {
                name: "test3",
                created_at: "09.06.2020",
                username: "testUser",
                thumbnail: "/public/img/uploads/example.jpg"
            },
            {
                name: "test4",
                created_at: "09.06.2020",
                username: "testUser",
                thumbnail: "/public/img/uploads/example.jpg"
            },
            {
                name: "test5",
                created_at: "09.06.2020",
                username: "testUser",
                thumbnail: "/public/img/uploads/example.jpg"
            },
            {
                name: "test6",
                created_at: "09.06.2020",
                username: "testUser",
                thumbnail: "/public/img/uploads/example.jpg"
            },
        ]
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
                        element.thumbnail = "/public/img/uploads/" + element.thumbnail;
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