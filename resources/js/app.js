import { createApp } from 'vue';
import QuotationForm from './components/QuotationForm.vue';

const token = document.querySelector('meta[name="api-token"]')?.content ?? '';
const currencies = JSON.parse(document.querySelector('meta[name="currencies"]')?.content ?? '[]');

createApp(QuotationForm, { token, action: '/api/quotation', currencies}).mount('#app');