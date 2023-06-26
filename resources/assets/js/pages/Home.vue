<template>
    <div>
        <main>
            <div class="title">
                <h1 class="text-gradient">OcCompare</h1>
                <div v-if="(occupation_1 && occupation_2 && loading) || result">
                    <h3>Comparing <span>{{ occupation_1.title }}</span> & <span>{{ occupation_2.title }}</span></h3>
                    <transition name="fade">
                        <button v-show="result && occupation_1 && occupation_2" v-if="result"
                            @click="resetApp">Restart</button>
                    </transition>
                </div>
                <div v-else>
                    <h3>Find skill overlaps in different occupations</h3>
                    <p>Thought about a career change but worried the skills you have now may not translate to your next
                        passion? No Stress! Simply select 2 industries in the dropdown menus and find out how your skills
                        overlap in a new occupation!</p>
                </div>
            </div>

            <div>
                <transition name="fade" mode="out-in">
                    <SelectOccupation @getmatch="compare" v-if="!loading && !occupation_1" />

                    <div v-if="loading" class="loading">
                        <h3>Computer loading results</h3>
                        <Loader />
                    </div>

                    <MatchResult v-if="result && !loading" :result="result" />
                </transition>
            </div>
        </main>
    </div>
</template>

<script>
import MatchResult from '../components/MatchResult';
import SelectOccupation from '../components/form-controls/SelectOccupation';
import Loader from '../components/Loader';
export default {
    name: 'home-page',
    components: {
        SelectOccupation,
        MatchResult,
        Loader,
    },
    data() {
        return {
            loading: false,
            occupation_1: null,
            occupation_2: null,
            result: null,
        }
    },
    methods: {
        compare(occupations) {
            /**
             * There is a case with the api where an occupation cant be fetched
             * from the service. I don't have time to find out which ones are 
             * doing this but i have seen it happen. In the case we catch an 
             * error, api return error 500. 
             * 
             * Here we would go back to select and show an error message saying 
             * could not get result. and allow them to pick again. 
             * 
             * Possihble functionality would be to remove the broken search
             * term and maybe even explain why its not there anymore.
             * 
             */
            this.loading = true;

            this.occupation_1 = occupations[0]
            this.occupation_2 = occupations[1]

            this.axios.post('/api/compare', {
                occupation_1: this.occupation_1.code,
                occupation_2: this.occupation_2.code
            }).then((response) => {
                this.loading = false;
                this.result = response.data
                this.history.push(result)
            }).catch(() => {
                this.loading = false;
            });
        },
        resetApp() {
            /**
             * The app ui is rendered based on the value of these variables
             * so we set them back to null to go back to the inital app state
             * 
             */
            this.occupation_1 = null
            this.occupation_2 = null
            this.result = null
        }
    }
}
</script>

<style lang="scss" scoped>
main {
    margin: 150px auto;
    max-width: 1000px;
    padding: 15px 15px;
    min-height: 600px;
    height: 600px;
    border-radius: 15px;
    transition: 250ms ease;
    display: grid;
    grid-template-columns: 1fr 1fr;
    position: relative;
    grid-gap: 25px;
}

.title * {
    margin: 0;
    margin-bottom: 15px;
}

.result {
    position: absolute;
    top: -100px;
}

h1 {
    font-weight: 600;
    font-size: 60px;
    background-image: linear-gradient(45deg, #3097D1 15%, #0b4a6f, 35%, #40b9ff 90%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    -moz-background-clip: text;
    -moz-text-fill-color: transparent;
}

h3 span {
    color: #3097D1;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}


.loading h3 {
    text-align: center;
}
</style>