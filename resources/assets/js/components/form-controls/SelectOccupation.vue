<template>
    <section id="step-1">
        <div class="occupation-form">
            <div class="form-field" v-for="n in 2">
                <label for="occupation_one">Occupation {{ n }}</label>
                <select name="occupation_one" id="occupation_one" v-model="selected[n - 1]">
                    <option value="">Select an occupation</option>
                    <option v-for="occupation in occupations" :value="occupation">{{ occupation.title
                    }}
                    </option>
                </select>
            </div>

            <div class="error" v-if="error">{{ error }}</div>
            <button @click="getMatch">Match!</button>

        </div>
    </section>
</template>

<script>
export default {
    name: 'select-occupation',
    data() {
        return {
            occupations: [],
            selected: ["", ""],
            error: ""
        };
    },
    async created() {
        await this.axios.get('/api/occupations').then((response) => {
            this.occupations = response.data;
        })
    },
    methods: {
        getMatch() {
            /**
             * Here i use the selected array to check for errors
             * for readability we could use an object with descriptive
             * key names to make the code more readable, this will do 
             * for now
             */
            this.error = ""
            if (!this.selected[0] && !this.selected[1]) {
                this.error = "Please fill both occupations"
                return
            }
            if (this.selected[0] && !this.selected[1]) {
                this.error = "Please select occupation 2"
                return
            }
            if (!this.selected[0] && this.selected[1]) {
                this.error = "Please select occupation 1"
                return
            }

            /**
             * We do this check as there is no need to compare two things that are
             * the same. It a request that doesnt need to get made.
             * 
             * In the case we want the users to be able to see the scores of the
             * different occupations alone, we would create a seperate
             *  functionality for browsing. The purpose of this application is 
             * to find the difference in two DIFFERENT occupations.
             */
            if (this.selected[0] == this.selected[1]) {
                this.error = "You cannot select the same occupation twice"
                return
            }
            this.$emit('getmatch', this.selected)
        }
    },
}
</script>

<style lang="scss" scoped>
.occupation-form {
    padding: 15px;
    width: fit-content;
    margin: 0 auto;
}

.form-field {
    align-self: center;
    ;
    margin-left: auto;
    display: flex;
    flex-direction: column;
    margin-bottom: 15px;
}

.form-field label {
    font-weight: bold;
    font-size: 20px;
    margin: 0;
}

.form-field select {
    min-width: 200px;
    width: 300px;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #5d5d5d5d;
    box-shadow: 2px 2px 2px #88888888;
    background-color: white;
}

.error {
    width: 100%;
    color: #bf5329;
    text-align: center;
    margin: 15px 0;
}
</style>