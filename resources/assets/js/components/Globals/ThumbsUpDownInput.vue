<template>
    <ul class="list-inline">
        <li class="list-inline-item" v-for="item in items">
            <a href="#" @click.prevent="selectItem(item)"><icon v-b-tooltip.hover :title="item.value" :scale="2" :class="{active: value === item.value}" :name="value === item.value ? item.icon : item.alt_icon"/></a>
        </li>
    </ul>
</template>

<script>
    export default {
        props: {
            value: {
                default: null,
            },
            readonly: {
                type: Boolean,
                default: false,
            }
        },
        data() {
            return {
                items: [
                    {
                        icon: 'thumbs-down',
                        alt_icon: 'thumbs-o-down',
                        value: 'Thumbs down',
                    },
                    {
                        icon: 'thumbs-up',
                        alt_icon: 'thumbs-o-up',
                        value: 'Thumbs up'
                    },
                ]
            }
        },
        methods: {
            selectItem(item) {
                if (this.readonly) {
                    return;
                }
                this.$emit('input', this.value === item.value ? 'No opinion' : item.value);
            }
        }
    }
</script>

<style lang="scss" scoped>
    .fa-icon {
        color: lightgrey;
    }
    .fa-icon.active {
        color: #20a8d8;
    }
</style>