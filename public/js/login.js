import navbar from './components/navigation.js';

const app = new Vue({
    el: "#root",
    data: {
        username: "",
        password: ""
    },
    components: {
        navbar
    },
    methods: {
        login(e) {
            e.preventDefault();

            let formData = new FormData();
            formData.append("username", this.username);
            formData.append("password", this.password);

            axios.post("/api/auth", formData)
            .then(response => {
                setCookie("token", `Bearer ${response.data.data.token}`, "30");

                Swal.fire({
                    title: "Success!",
                    text: "The login has been successful!",
                    icon: "success",
                    timer: 1500,
                    showConfirmButton: false
                }).then(result => {
                    window.location.replace("/panel");
                });
            })
            .catch(error => {
                if(error.response) {
                    Swal.fire(
                        "Error!",
                        error.response.data.message,
                        "error"
                    );
                }
            })

        }
    }
});