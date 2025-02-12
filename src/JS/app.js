document.addEventListener('DOMContentLoaded', function () {
    // Variables globales
    let listas = [];
    try {
        const listasGuardadas = localStorage.getItem('listas');
        if (listasGuardadas) {
            listas = JSON.parse(listasGuardadas);
        }
    } catch (error) {
        console.error('Error al cargar las listas:', error);
        localStorage.removeItem('listas'); // Limpiar localStorage si hay datos corruptos
    }

    // Verificar si el usuario está autenticado
    const usuarioId = document.body.dataset.userId;
    const estaAutenticado = !!usuarioId;

    // Cargar listas desde el servidor si el usuario está autenticado
    if (estaAutenticado) {
        fetch('/guardar-listas.php')
            .then(response => response.json())
            .then(listasServidor => {
                if (Array.isArray(listasServidor)) {
                    listas = listasServidor;
                    guardarListas();
                    mostrarListas();
                }
            })
            .catch(error => console.error('Error al cargar listas:', error));
    }

    // Elementos DOM
    const modal = document.getElementById('modal');
    const modalTarea = document.getElementById('modalTarea');
    const modalEditar = document.getElementById('modalEditar');
    const btnNuevaLista = document.getElementById('nuevaLista');
    const formLista = document.getElementById('formLista');
    const formTarea = document.getElementById('formTarea');
    const formEditar = document.getElementById('formEditar');
    const listasContainer = document.getElementById('listas');

    // Event Listeners
    btnNuevaLista.addEventListener('click', () => {
        formLista.reset();  // Limpiar el formulario
        modal.style.display = 'block';  // Mostrar el modal
    });

    // Cerrar modales
    document.querySelectorAll('.modal').forEach(modalElement => {
        const cerrar = modalElement.querySelector('.cerrar');
        if (cerrar) {
            cerrar.addEventListener('click', () => {
                modalElement.style.display = 'none';
            });
        }
        modalElement.addEventListener('click', (e) => {
            if (e.target === modalElement) {
                modalElement.style.display = 'none';
            }
        });
    });

    // Crear lista
    formLista.addEventListener('submit', function (e) {
        e.preventDefault();
        const titulo = document.getElementById('titulo').value.trim();
        if (titulo) {
            const nuevaLista = {
                id: Date.now(),
                titulo,
                tareas: []
            };
            listas.push(nuevaLista);
            guardarListas();
            mostrarListas();
            modal.style.display = 'none';
            formLista.reset();
        }
    });

    // Crear tarea
    formTarea.addEventListener('submit', function (e) {
        e.preventDefault();
        const listaId = parseInt(document.getElementById('listaId').value);
        const descripcion = document.getElementById('descripcionTarea').value.trim();
        if (descripcion) {
            const lista = listas.find(l => l.id === listaId);
            if (lista) {
                lista.tareas.push({
                    id: Date.now(),
                    texto: descripcion,
                    completada: false
                });
                guardarListas();
                mostrarListas();
            }
            modalTarea.style.display = 'none';
            formTarea.reset();
        }
    });

    // Editar elemento
    formEditar.addEventListener('submit', function (e) {
        e.preventDefault();
        const id = parseInt(document.getElementById('elementoId').value);
        const tipo = document.getElementById('tipoElemento').value;
        const nuevoTexto = document.getElementById('nuevoTexto').value.trim();

        if (tipo === 'lista') {
            const lista = listas.find(l => l.id === id);
            if (lista) lista.titulo = nuevoTexto;
        } else {
            const listaId = parseInt(document.getElementById('elementoId').dataset.listaId);
            const lista = listas.find(l => l.id === listaId);
            if (lista) {
                const tarea = lista.tareas.find(t => t.id === id);
                if (tarea) tarea.texto = nuevoTexto;
            }
        }
        guardarListas();
        mostrarListas();
        modalEditar.style.display = 'none';
    });

    // Funciones auxiliares
    function guardarListas() {
        localStorage.setItem('listas', JSON.stringify(listas));

        // Si el usuario está autenticado, sincronizar con el servidor
        if (estaAutenticado) {
            fetch('/guardar-listas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(listas)
            })
                .catch(error => console.error('Error al sincronizar:', error));
        }
    }

    function mostrarListas() {
        listasContainer.innerHTML = '';
        listas.forEach(lista => {
            listasContainer.innerHTML += `
                <div class="lista">
                    <div class="lista-header">
                        <h3>${lista.titulo}</h3>
                        <div class="lista-acciones">
                            <button onclick="editarElemento('lista', ${lista.id})" class="btn-accion">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="eliminarLista(${lista.id})" class="btn-accion">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="lista-tareas">
                        <div class="lista-tareas-header">
                            <button class="btn-toggle-tareas" onclick="toggleTareas(${lista.id})">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <button onclick="mostrarModalTarea(${lista.id})" class="btn btn-nueva-lista">
                                <i class="fas fa-plus"></i> Nueva Tarea
                            </button>
                        </div>
                        <div class="tareas-contenido" id="tareas-${lista.id}" style="display: none;">
                            ${lista.tareas.map((tarea, i) => `
                                <div class="tarea-item ${tarea.completada ? 'completada' : ''}">
                                    <span class="tarea-numero">${i + 1}.</span>
                                    <button onclick="toggleTarea(${lista.id}, ${tarea.id})" class="btn-tarea estado">
                                        <i class="fas ${tarea.completada ? 'fa-check-circle' : 'fa-circle'}"></i>
                                    </button>
                                    <span class="tarea-texto">${tarea.texto}</span>
                                    <div class="tarea-acciones">
                                        <button onclick="editarElemento('tarea', ${tarea.id}, ${lista.id})" class="btn-accion">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="eliminarTarea(${lista.id}, ${tarea.id})" class="btn-accion">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
        });
    }

    // Funciones globales
    window.mostrarModalTarea = function (listaId) {
        document.getElementById('listaId').value = listaId;
        modalTarea.style.display = 'block';
    };

    window.editarElemento = function (tipo, id, listaId = null) {
        const elemento = tipo === 'lista'
            ? listas.find(l => l.id === id)
            : listas.find(l => l.id === listaId).tareas.find(t => t.id === id);

        document.getElementById('elementoId').value = id;
        document.getElementById('tipoElemento').value = tipo;
        if (listaId) document.getElementById('elementoId').dataset.listaId = listaId;
        document.getElementById('nuevoTexto').value = tipo === 'lista' ? elemento.titulo : elemento.texto;
        modalEditar.style.display = 'block';
    };

    window.eliminarLista = function (id) {
        mostrarModalConfirmacion('¿Estás seguro de eliminar esta lista?', () => {
            listas = listas.filter(l => l.id !== id);
            guardarListas();
            mostrarListas();
        });
    };

    window.eliminarTarea = function (listaId, tareaId) {
        mostrarModalConfirmacion('¿Estás seguro de eliminar esta tarea?', () => {
            const lista = listas.find(l => l.id === listaId);
            if (lista) {
                lista.tareas = lista.tareas.filter(t => t.id !== tareaId);
                guardarListas();
                mostrarListas();
            }
        });
    };

    window.toggleTarea = function (listaId, tareaId) {
        const lista = listas.find(l => l.id === listaId);
        if (lista) {
            const tarea = lista.tareas.find(t => t.id === tareaId);
            if (tarea) {
                tarea.completada = !tarea.completada;
                guardarListas();
                mostrarListas();
            }
        }
    };

    window.toggleTareas = function (listaId) {
        const tareasContenido = document.getElementById(`tareas-${listaId}`);
        const botonToggle = tareasContenido.previousElementSibling.querySelector('.btn-toggle-tareas');

        if (tareasContenido.style.display === 'none') {
            tareasContenido.style.display = 'block';
            botonToggle.classList.add('active');
        } else {
            tareasContenido.style.display = 'none';
            botonToggle.classList.remove('active');
        }
    };

    function mostrarModalConfirmacion(mensaje, onConfirm) {
        const modalConfirmacion = document.createElement('div');
        modalConfirmacion.className = 'modal';
        modalConfirmacion.style.display = 'block';
        modalConfirmacion.innerHTML = `
            <div class="modal-contenido">
                <span class="cerrar">&times;</span>
                <h2>Confirmar</h2>
                <p>${mensaje}</p>
                <div class="botones">
                    <button class="btn">Confirmar</button>
                    <button class="btn btn-cancelar">Cancelar</button>
                </div>
            </div>
        `;
        document.body.appendChild(modalConfirmacion);

        // Eventos del modal
        const cerrar = modalConfirmacion.querySelector('.cerrar');
        const btnConfirmar = modalConfirmacion.querySelector('.btn');
        const btnCancelar = modalConfirmacion.querySelector('.btn-cancelar');

        cerrar.onclick = () => modalConfirmacion.remove();
        btnCancelar.onclick = () => modalConfirmacion.remove();
        btnConfirmar.onclick = () => {
            onConfirm();
            modalConfirmacion.remove();
        };
    }

    // Inicialización
    mostrarListas();
});
