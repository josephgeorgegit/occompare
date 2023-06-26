<template>
    <section id="step-2">
        <h2>{{ roundFloat(result.match) }}% skill overlap</h2>
        <p :style="`color: ${similarMessage.color}`">{{ similarMessage.message }}</p>
        <h3>Top related skills</h3>
        <div>
            <ul v-if="result.matching">
                <div v-for="skill, key, index in result.matching">
                    <h5 v-if="index == 3">Other related skills</h5>
                    <li>
                        <h5 v-if="index < 3">{{ key }}</h5>
                        <p v-if="index >= 3">{{ key }}</p>
                        <p v-if="index < 3">{{ skill.description }}</p>
                    </li>
                </div>
            </ul>
        </div>

    </section>
</template>

<script>
export default {
    name: "match-result",
    props: ["result"],
    methods: {
        roundFloat(input) {
            return Math.round(input)
        }
    },
    computed: {
        /**
         * Most of the time, the score generated is above 50%, so the number alone 
         * could potentially be misleading if you havent used the app heaps
         * to get a feel for scores.
         * 
         * We provide this coloured message so first time user can get an idea of 
         * what a good score is. 
         */
        similarMessage() {
            let match = this.result.match
            return match > 60 && match < 70 ? { message: 'Not too disimilar', color: "orange" } :
                match >= 70 && match < 90 ? { message: 'Quite similar', color: "green" } :
                    match >= 90 ? { message: 'Basically the same!', color: "green" } :
                        { message: 'Not similar', color: "red" }
        }
    }
}

</script>

<style lang="scss" scoped>
h4,
h5 {
    font-weight: bold;
}

ul {
    list-style: none;
    padding-left: 0;
}
</style>