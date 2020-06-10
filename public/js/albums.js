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
            }
        ],
        noNewData: 0
    },
    components: {
        navbar
    },
    methods: {
        loadAlbums() {
            // Check if we received new data on the latest requests, else abort it
            if(this.noNewData < 4) {
                axios.get(`/api/albums/${this.limit}/${this.offset}`)
                .then(response => {
                    if(response.data.data.length > 0) {
                        response.data.data.forEach(element => {
                            element.thumbnail = "/public/img/uploads/" + element.thumbnail;
                            this.albums.push(element);
                        });
    
                        this.offset += this.limit;
                        this.noNewData = 0;
                    } else {
                        this.noNewData++;
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
        inifintyScroll() {
            window.onscroll = () => {
                let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;
            
                if (bottomOfWindow) {
                    if(!this.isLoading) {
                        this.loadUrls();
                    }
                }
            }
        }
    },
    mounted() {
        this.loadAlbums();
        this.inifintyScroll();
    }
});