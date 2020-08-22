<template>
    <v-app>
        <RouterView :key="$route.fullPath"/>
    </v-app>
</template>

<script>
    export default {
        name: 'App'
    }
</script>
