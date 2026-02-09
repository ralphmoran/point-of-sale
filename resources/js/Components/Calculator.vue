<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="visible" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <!-- Backdrop — tapping this closes -->
                <div class="fixed inset-0 bg-gray-900/50" @click="close"></div>
                <div class="relative bg-white rounded-2xl shadow-xl w-80 p-5" @click.stop>
                    <!-- Close button -->
                    <button @click="close" class="absolute top-3 right-3 p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <!-- Display -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <p class="text-right text-3xl font-mono font-semibold text-gray-900 truncate">{{ display }}</p>
                    </div>

                    <!-- Buttons -->
                    <div class="grid grid-cols-4 gap-2">
                        <button v-for="btn in buttons" :key="btn.label" @click="press(btn)"
                            class="h-12 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center active:scale-95"
                            :class="btnClass(btn)"
                            :style="btn.span ? `grid-column: span ${btn.span}` : ''">
                            {{ btn.label }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, watch, onUnmounted } from 'vue';
import { useCalculator } from '@/composables/useCalculator';

const { visible, close } = useCalculator();

const display = ref('0');
const prevValue = ref(null);
const operator = ref(null);
const waitingForOperand = ref(false);

const buttons = [
    { label: 'C', type: 'fn', action: 'clear' },
    { label: '+/-', type: 'fn', action: 'negate' },
    { label: '%', type: 'fn', action: 'percent' },
    { label: '÷', type: 'op', action: '/' },
    { label: '7', type: 'num' }, { label: '8', type: 'num' }, { label: '9', type: 'num' },
    { label: '×', type: 'op', action: '*' },
    { label: '4', type: 'num' }, { label: '5', type: 'num' }, { label: '6', type: 'num' },
    { label: '−', type: 'op', action: '-' },
    { label: '1', type: 'num' }, { label: '2', type: 'num' }, { label: '3', type: 'num' },
    { label: '+', type: 'op', action: '+' },
    { label: '0', type: 'num', span: 2 }, { label: '.', type: 'num' },
    { label: '=', type: 'op', action: '=' },
];

const btnClass = (btn) => {
    if (btn.type === 'op') return 'bg-primary-600 text-white hover:bg-primary-700';
    if (btn.type === 'fn') return 'bg-gray-200 text-gray-800 hover:bg-gray-300';
    return 'bg-white border border-gray-200 text-gray-800 hover:bg-gray-50';
};

const calculate = (a, b, op) => {
    const n1 = parseFloat(a), n2 = parseFloat(b);
    switch (op) {
        case '+': return n1 + n2;
        case '-': return n1 - n2;
        case '*': return n1 * n2;
        case '/': return n2 !== 0 ? n1 / n2 : 'Error';
        default: return n2;
    }
};

const press = (btn) => {
    if (btn.type === 'num') {
        if (btn.label === '.') {
            if (waitingForOperand.value) { display.value = '0.'; waitingForOperand.value = false; return; }
            if (!display.value.includes('.')) display.value += '.';
            return;
        }
        if (waitingForOperand.value) { display.value = btn.label; waitingForOperand.value = false; return; }
        display.value = display.value === '0' ? btn.label : display.value + btn.label;
        return;
    }
    if (btn.action === 'clear') {
        display.value = '0'; prevValue.value = null; operator.value = null; waitingForOperand.value = false;
        return;
    }
    if (btn.action === 'negate') { display.value = String(parseFloat(display.value) * -1); return; }
    if (btn.action === 'percent') { display.value = String(parseFloat(display.value) / 100); return; }

    if (prevValue.value !== null && operator.value && !waitingForOperand.value) {
        const result = calculate(prevValue.value, display.value, operator.value);
        display.value = String(result);
        prevValue.value = result;
    } else {
        prevValue.value = parseFloat(display.value);
    }

    if (btn.action === '=') { operator.value = null; prevValue.value = null; }
    else { operator.value = btn.action; }
    waitingForOperand.value = true;
};

const onKeydown = (e) => {
    if (!visible.value) return;
    if (e.key === 'Escape') { close(); return; }
    if (e.key >= '0' && e.key <= '9') { press({ label: e.key, type: 'num' }); return; }
    if (e.key === '.') { press({ label: '.', type: 'num' }); return; }
    if (e.key === '+') { press({ label: '+', type: 'op', action: '+' }); return; }
    if (e.key === '-') { press({ label: '−', type: 'op', action: '-' }); return; }
    if (e.key === '*') { press({ label: '×', type: 'op', action: '*' }); return; }
    if (e.key === '/') { e.preventDefault(); press({ label: '÷', type: 'op', action: '/' }); return; }
    if (e.key === 'Enter' || e.key === '=') { press({ label: '=', type: 'op', action: '=' }); return; }
    if (e.key === 'Backspace') {
        display.value = display.value.length > 1 ? display.value.slice(0, -1) : '0';
    }
};

watch(visible, (v) => {
    if (v) window.addEventListener('keydown', onKeydown);
    else window.removeEventListener('keydown', onKeydown);
});

onUnmounted(() => window.removeEventListener('keydown', onKeydown));
</script>
