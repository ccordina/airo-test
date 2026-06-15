<template>
    <div>
        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="age" class="block text-sm font-medium text-slate-700">Age</label>
                <input
                    id="age"
                    v-model="form.age"
                    type="text"
                    required
                    placeholder="Comma-separated ages between 18 and 70, e.g. 28,35"
                    class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                >
            </div>

            <div>
                <label for="currency" class="block text-sm font-medium text-slate-700">Currency</label>
                <select
                    id="currency_id"
                    v-model="form.currency_id"
                    required
                    class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                >
                    <option value="" disabled>Select currency</option>
                    <option v-for="currency in currencies" :value="currency.value" :key="currency.value">
                        {{ currency.label }}
                    </option>
                </select>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-slate-700">Start date</label>
                    <input
                        id="start_date"
                        v-model="form.start_date"
                        type="date"
                        required
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                    >
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-slate-700">End date</label>
                    <input
                        id="end_date"
                        v-model="form.end_date"
                        type="date"
                        required
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                    >
                </div>
            </div>

            <button
                type="submit"
                :disabled="loading"
                class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
            >
                {{ loading ? 'Calculating…' : 'Calculate quote' }}
            </button>
        </form>

        <div
            v-if="result"
            class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-5"
        >
            <p class="text-sm font-medium text-emerald-700">Your quote</p>
            <p class="mt-1 text-3xl font-semibold tracking-tight text-emerald-900">
                {{ formattedTotal }}
            </p>
            <p class="mt-2 text-xs text-emerald-700">
                Quotation #{{ result.quotation_id }}
            </p>
        </div>

        <div
            v-if="error"
            class="mt-6 whitespace-pre-line rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800"
        >
            {{ error }}
        </div>
    </div>
</template>

<script>
export default {
    props: {
        token: String,
        action: String,
        currencies: {
            type: Array,
            default: () => [],
        },
    },

    data() {
        return {
            form: {
                age: '',
                currency_id: '',
                start_date: '',
                end_date: '',
            },
            result: null,
            error: '',
            loading: false,
        };
    },

    computed: {
        formattedTotal() {
            if (!this.result) return '';

            try {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: this.result.currency_id,
                }).format(this.result.total);
            } catch {
                return `${this.result.total} ${this.result.currency_id}`;
            }
        },
    },

    methods: {
        async submit() {
            this.error = '';
            this.result = null;
            this.loading = true;

            try {
                const res = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        Authorization: `Bearer ${this.token}`,
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await res.json().catch(() => null);

                if (!res.ok) {
                    this.error = data?.errors
                        ? Object.values(data.errors).flat().join('\n')
                        : (data?.error ?? 'Request failed');
                    return;
                }

                this.result = data;
            } catch (e) {
                this.error = e.message || 'Network error';
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>