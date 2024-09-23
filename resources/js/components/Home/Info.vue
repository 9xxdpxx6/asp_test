<template>
    <div class="mt-5">
        <h2 class="text-center">Контакты</h2>
        <div class="row">
            <div class="col-md-6" v-for="branch in branches" :key="branch.id">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{ branch.name }}</h5>
                        <p>{{ branch.address }}</p>
                        <a :href="'/contact/' + branch.id" class="btn btn-outline-primary">Подробнее</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Info',
    data() {
        return {
            branches: [
                { id: 1, name: 'Филиал 1', address: 'Улица 1, Город 1' },
                { id: 2, name: 'Филиал 2', address: 'Улица 2, Город 2' },
            ],
        };
    },
};
</script>
