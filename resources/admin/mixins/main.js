import Rest from "@/utils/http/Rest.js";
import Storage from "@/utils/Storage";
import { titleCase } from "@/misc/functions";

const config = window.fluentFrameworkAdmin;

export default {
    data() {
        return {
            Storage,
        };
    },
    methods: {
        $t: (key) => {
            return key;
        },
        $get: Rest.get,
        $post: Rest.post,
        $put: Rest.put,
        $patch: Rest.patch,
        $del: Rest.delete,
        $download: (route) => {
            let baseUrl = "";

            try {
                new URL(route);
            } catch {
                baseUrl = config.rest.url;
            }

            if (route.startsWith("/") && baseUrl.endsWith("/")) {
                route = route.substring(1);
            }

            window.location.href = baseUrl + route;
        },
        $formatNumber(amount, hideEmpty = false) {
            if (!amount && hideEmpty) {
                return "";
            }

            if (!amount) {
                amount = "0";
            }

            return new Intl.NumberFormat("en-US").format(amount);
        },
        $changeTitle(title) {
            document.querySelector("head title").textContent =
                title + " - " + titleCase(config.slug);
        },
        $handleError(response) {
            let errorMessage = "";

            if (typeof response === "string") {
                errorMessage = response;
            } else if ("message" in response) {
                errorMessage = response.message;
            } else {
                if (response.status === 422) {
                    errorMessage = "Data validation failed. Please try again.";
                }
            }

            this.$notifyError(errorMessage || "Something went wrong");
        },
    },
};
