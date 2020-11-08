export default {
    methods: {
        prevRouteIfExists(route) {
            const prevRoute = new URL(location.href).searchParams.get('prevRoute');
            if (prevRoute) {
                return prevRoute;
            } else {
                return route;
            }
        }
    }
};
