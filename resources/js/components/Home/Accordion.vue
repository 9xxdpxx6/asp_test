<template>
    <div class="mt-5">
        <h2 class="text-center">Дополнительная информация</h2>
        <div class="accordion" id="schoolAccordion">
            <div class="accordion-item" v-for="(info, index) in accordionInfo" :key="index">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapse' + index">
                        {{ info.title }}
                    </button>
                </h2>
                <div :id="'collapse' + index" class="accordion-collapse collapse">
                    <div class="accordion-body">{{ info.content }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Accordion',
    data() {
        return {
            accordionInfo: [
                { title: 'Our Students', content: 'Information about students...' },
                { title: 'Our Instructors', content: 'Information about instructors...' },
                { title: 'Our Fleet', content: 'Information about the vehicles...' },
            ],
        };
    },
};
</script>
