// import axios from "axios";
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
const user = reactive({
    name: "",
    email: "",
});

export default function useAuth() {
    const processing = ref(false);
    const validationErrors = ref({});
    const router = useRouter();
    const loginForm = reactive({
        email: "",
        password: "",
        remember: false,
    });
    const registerForm = reactive({
        name: "",
        email: "",
        password: "",
    });
    
    const submitRegister = async () => {
        console.log(registerForm);
        if (processing.value) return;
        processing.value = true;
        validationErrors.value = {};
        axios
            .post("/api/register", registerForm)
            .then((response) => {
                router.push({ name: "login" });
            })
            .catch((error) => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors;
                    isLoading.value = false;
                }
            });
    };

    const submitLogin = async () => {
        if (processing.value) return;
        processing.value = true;
        validationErrors.value = {};

        axios
            .post("/login", loginForm)
            .then(async (response) => {
                loginUser(response);
            })
            .catch((error) => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors;
                }
            })
            .finally(() => (processing.value = false));
    };

    const loginUser = (response) => {
        user.name = response.data.name;
        user.email = response.data.email;
        console.log(user.name);
        localStorage.setItem("loggedIn", JSON.stringify(true));
        router.push({ name: "posts.index" });
    };

    const getUser = () => {
        axios.get("/api/user").then((response) => {
            // console.log(response);
            loginUser(response);
        });
    };

    return {
        loginForm,
        validationErrors,
        processing,
        submitLogin,
        registerForm,
        submitRegister,
        user,
        getUser,
    };
}
