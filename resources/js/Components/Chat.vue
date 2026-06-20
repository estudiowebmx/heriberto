<script setup>
import { ref, reactive, nextTick, watch } from "vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

const messages = ref([]);
const input = ref("");
const loading = ref(false);
const conversationId = ref(null);
const chatContainer = ref(null);
const showForm = ref(false);

const form = reactive({
    nombre: "",
    apellido_paterno: "",
    apellido_materno: "",
    telefono: "",
    seccion: "",
    municipio: "",
    distrito_local: "",
    distrito_federal: "",
    demarcacion: "",
    dmunicipal: "",
});

const scrollToBottom = async () => {
    await nextTick();
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
};

const sendMessage = async () => {
    const text = input.value.trim();
    if (!text || loading.value) return;

    input.value = "";
    messages.value.push({ role: "user", content: text });
    loading.value = true;
    messages.value.push({ role: "assistant", content: "", loading: true });
    await scrollToBottom();

    try {
        const res = await axios.post("/chat", {
            message: text,
            conversation_id: conversationId.value,
        });
        messages.value.pop();
        messages.value.push({
            role: "assistant",
            content: res.data.message,
            loading: false,
        });
        conversationId.value = res.data.conversation_id;
    } catch (err) {
        messages.value.pop();
        messages.value.push({
            role: "assistant",
            content:
                "Lo siento, ocurrio un error al procesar tu mensaje. Por favor intenta de nuevo.",
            loading: false,
        });
    } finally {
        loading.value = false;
        await scrollToBottom();
    }
};

const buscarSeccion = async () => {
    const val = form.seccion.trim();
    if (!val) {
        form.municipio = "";
        form.distrito_local = "";
        form.distrito_federal = "";
        return;
    }
    try {
        const res = await axios.get("/api/seccion", { params: { q: val } });
        form.municipio = res.data.municipio;
        form.distrito_local = res.data.distrito_local;
        form.distrito_federal = res.data.distrito_federal;
        form.demarcacion = res.data.demarcacion || "";
        form.dmunicipal = res.data.dmunicipal || "";
    } catch {
        form.municipio = "";
        form.distrito_local = "";
        form.distrito_federal = "";
        form.demarcacion = "";
        form.dmunicipal = "";
    }
};

const submitRegistro = async () => {
    if (!form.nombre || !form.apellido_paterno || !form.telefono) return;
    if (form.telefono.replace(/\D/g, "").length !== 10) return;
    if (loading.value) return;

    showForm.value = false;
    loading.value = true;
    messages.value.push({ role: "assistant", content: "", loading: true });
    await scrollToBottom();

    try {
        const res = await axios.post("/chat/registrar", { ...form });
        messages.value.pop();
        messages.value.push({
            role: "assistant",
            content: res.data.message,
            loading: false,
        });
        Object.keys(form).forEach((k) => (form[k] = ""));
    } catch (err) {
        messages.value.pop();
        messages.value.push({
            role: "assistant",
            content:
                "Lo siento, ocurrio un error al procesar tu registro. Por favor intenta de nuevo.",
            loading: false,
        });
    } finally {
        loading.value = false;
        await scrollToBottom();
    }
};

const handleKeydown = (e) => {
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
};

const renderMarkdown = (content) => {
    if (!content) return "";
    return content
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
        .replace(/__(.*?)__/g, "<strong>$1</strong>")
        .replace(/\*(.*?)\*/g, "<em>$1</em>")
        .replace(/_(.*?)_/g, "<em>$1</em>")
        .replace(/\n/g, "<br>");
};
</script>

<template>
    <div class="flex h-[calc(100vh-4rem)] flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0"
                >
                    <img
                        src="/img/perfil.jpg"
                        alt="Jasmin Bugarin"
                        class="w-full h-full object-cover"
                    />
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">
                        Jasmin Bugarin
                    </h1>
                    <p class="text-sm text-gray-500">
                        Senadora de la república por Nayarit
                    </p>
                </div>
            </div>
        </div>

        <div
            ref="chatContainer"
            class="flex-1 overflow-y-auto px-4 py-6 space-y-4 bg-gray-50"
        >
            <div class="flex justify-center mb-4">
                <div class="bg-white rounded-2xl shadow-sm border border-verde-200 max-w-lg w-full overflow-hidden">
                    <div class="h-36 overflow-hidden">
                        <img src="/img/portada.jpg" alt="Jasmin Bugarin" class="w-full h-full object-cover" />
                    </div>
                    <div class="px-6 pb-6 -mt-12 text-center">
                        <div class="w-20 h-20 rounded-full overflow-hidden mx-auto mb-3 border-4 border-white shadow-md">
                            <img src="/img/perfil.jpg" alt="Jasmin Bugarin" class="w-full h-full object-cover" />
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">¡Hola! Soy Jasmin Bugarin</h2>
                        <p class="text-gray-600 text-sm">Estoy aqui para platicar contigo, conocer tus inquietudes y compartirte mi vision para nuestro estado. ¿En que puedo ayudarte?</p>
                        <div class="flex flex-col gap-2 mt-4">
                            <button @click="showForm = true" class="flex items-center justify-between px-4 py-3 bg-verde-600 text-white rounded-lg hover:bg-verde-700 transition-colors font-medium text-sm w-full">
                                <span><i class="fa-solid fa-user-plus mr-3"></i>Unirme al Proyecto</span>
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                            <a href="/conoceme" class="flex items-center justify-between px-4 py-3 bg-verde-600 text-white rounded-lg hover:bg-verde-700 transition-colors font-medium text-sm w-full">
                                <span><i class="fa-solid fa-clipboard-question mr-3"></i>Conóceme</span>
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                            <Link v-if="$page.props.auth.user?.role === 'admin'" :href="route('dashboard')" class="flex items-center justify-between px-4 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition-colors font-medium text-sm w-full">
                                <span><i class="fa-solid fa-right-to-bracket mr-3"></i>Entrar a la plataforma</span>
                                <i class="fa-solid fa-chevron-right"></i>
                            </Link>
                            <Link v-else-if="$page.props.auth.user" :href="route('admin.personas.index')" class="flex items-center justify-between px-4 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition-colors font-medium text-sm w-full">
                                <span><i class="fa-solid fa-right-to-bracket mr-3"></i>Entrar a la plataforma</span>
                                <i class="fa-solid fa-chevron-right"></i>
                            </Link>
                            <Link v-else :href="route('login')" class="flex items-center justify-between px-4 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition-colors font-medium text-sm w-full">
                                <span><i class="fa-solid fa-right-to-bracket mr-3"></i>Entrar a la plataforma</span>
                                <i class="fa-solid fa-chevron-right"></i>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-for="(msg, idx) in messages"
                :key="idx"
                :class="[
                    'flex',
                    msg.role === 'user' ? 'justify-end' : 'justify-start',
                ]"
            >
                <div
                    :class="[
                        'max-w-[80%] rounded-2xl px-4 py-3',
                        msg.role === 'user'
                            ? 'bg-verde-600 text-white rounded-br-sm'
                            : 'bg-white border border-gray-100 shadow-sm rounded-bl-sm',
                    ]"
                >
                    <div
                        v-if="msg.loading"
                        class="flex items-center gap-2 text-gray-500"
                    >
                        <div class="flex gap-1">
                            <span
                                class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                style="animation-delay: 0s"
                            ></span>
                            <span
                                class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                style="animation-delay: 0.2s"
                            ></span>
                            <span
                                class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                style="animation-delay: 0.4s"
                            ></span>
                        </div>
                        <span class="text-sm">Escribiendo...</span>
                    </div>
                    <div
                        v-else
                        class="text-sm leading-relaxed"
                        :class="
                            msg.role === 'user' ? 'text-white' : 'text-gray-800'
                        "
                        v-html="renderMarkdown(msg.content)"
                    ></div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 bg-white px-4 py-4">
            <div class="max-w-4xl mx-auto flex gap-3">
                <textarea
                    v-model="input"
                    @keydown="handleKeydown"
                    placeholder="Escribe tu mensaje..."
                    rows="1"
                    class="flex-1 resize-none rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none"
                    :disabled="loading"
                ></textarea>
                <button
                    @click="sendMessage"
                    :disabled="loading || !input.trim()"
                    class="px-5 py-3 bg-verde-600 text-white rounded-xl hover:bg-verde-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium text-sm"
                >
                    Enviar
                </button>
            </div>
        </div>

        <Teleport to="body">
            <div
                v-if="showForm"
                class="fixed inset-0 z-50 flex items-center justify-center"
                @click.self="showForm = false"
            >
                <div
                    class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                ></div>
                <div
                    class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4 z-10 max-h-[90vh] overflow-y-auto"
                >
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Unirme al Proyecto
                        </h3>
                        <button
                            @click="showForm = false"
                            class="text-gray-400 hover:text-gray-600 text-xl leading-none"
                        >
                            &times;
                        </button>
                    </div>

                    <form @submit.prevent="submitRegistro" class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Nombre *</label
                            >
                            <input
                                v-model="form.nombre"
                                type="text"
                                required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Apellido Paterno *</label
                            >
                            <input
                                v-model="form.apellido_paterno"
                                type="text"
                                required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Apellido Materno</label
                            >
                            <input
                                v-model="form.apellido_materno"
                                type="text"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Telefono *</label
                            >
                            <input
                                v-model="form.telefono"
                                type="tel"
                                required
                                pattern="[0-9]{10}"
                                maxlength="10"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none"
                                placeholder="Ej: 5512345678"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Seccion Electoral</label
                            >
                            <div class="flex gap-2">
                                <input
                                    v-model="form.seccion"
                                    type="text"
                                    @blur="buscarSeccion"
                                    class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none"
                                    placeholder="Ingresa tu seccion"
                                />
                                <button
                                    type="button"
                                    @click="buscarSeccion"
                                    class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm"
                                >
                                    Buscar
                                </button>
                            </div>
                        </div>
                        <div v-if="form.municipio">
                            <label
                                class="block text-sm font-medium text-gray-500 mb-1"
                                >Municipio</label
                            >
                            <input
                                :value="form.municipio"
                                readonly
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                            />
                        </div>
                        <div v-if="form.distrito_local" class="flex gap-3">
                            <div class="flex-1">
                                <label
                                    class="block text-sm font-medium text-gray-500 mb-1"
                                    >Distrito Local</label
                                >
                                <input
                                    :value="form.distrito_local"
                                    readonly
                                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                                />
                            </div>
                            <div class="flex-1">
                                <label
                                    class="block text-sm font-medium text-gray-500 mb-1"
                                    >Distrito Federal</label
                                >
                                <input
                                    :value="form.distrito_federal"
                                    readonly
                                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                                />
                            </div>
                        </div>
                        <div v-if="form.dmunicipal">
                            <label
                                class="block text-sm font-medium text-gray-500 mb-1"
                                >Demarcación Municipal</label
                            >
                            <input
                                :value="form.dmunicipal"
                                readonly
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                            />
                        </div>
                        <button
                            type="submit"
                            :disabled="
                                !form.nombre ||
                                !form.apellido_paterno ||
                                form.telefono.replace(/\D/g, '').length !== 10
                            "
                            class="w-full py-2.5 bg-verde-600 text-white rounded-lg hover:bg-verde-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium text-sm"
                        >
                            Registrarme
                        </button>
                    </form>
                </div>
            </div>
        </Teleport>
    </div>
</template>
