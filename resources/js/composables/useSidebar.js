import { ref } from 'vue';

const collapsed = ref(localStorage.getItem('sidebar_collapsed') === 'true');
const mobileOpen = ref(false);

export function useSidebar() {
    const toggleCollapsed = () => {
        collapsed.value = !collapsed.value;
        localStorage.setItem('sidebar_collapsed', collapsed.value);
    };

    const openMobile = () => { mobileOpen.value = true; };
    const closeMobile = () => { mobileOpen.value = false; };

    return { collapsed, mobileOpen, toggleCollapsed, openMobile, closeMobile };
}
