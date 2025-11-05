<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArriendaNet - Registro</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Crear Cuenta</h2>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Documento -->
                    <div>
                        <label for="doc_usuario" class="block text-sm font-medium text-gray-700">Documento</label>
                        <input type="text" name="doc_usuario" id="doc_usuario" required value="{{ old('doc_usuario') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Tipo de Documento -->
                    <div>
                        <label for="tip_documento" class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                        <select name="tip_documento" id="tip_documento" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccione...</option>
                            <option value="CC" {{ old('tip_documento') == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                            <option value="CE" {{ old('tip_documento') == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                            <option value="TI" {{ old('tip_documento') == 'TI' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                            <option value="PA" {{ old('tip_documento') == 'PA' ? 'selected' : '' }}>Pasaporte</option>
                            <option value="DNI" {{ old('tip_documento') == 'DNI' ? 'selected' : '' }}>DNI</option>
                            <option value="PEP" {{ old('tip_documento') == 'PEP' ? 'selected' : '' }}>PEP</option>
                            <option value="PPT" {{ old('tip_documento') == 'PPT' ? 'selected' : '' }}>PPT</option>
                        </select>
                    </div>

                    <!-- Nombres -->
                    <div>
                        <label for="pri_nombre" class="block text-sm font-medium text-gray-700">Primer Nombre</label>
                        <input type="text" name="pri_nombre" id="pri_nombre" required value="{{ old('pri_nombre') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="seg_nombre" class="block text-sm font-medium text-gray-700">Segundo Nombre</label>
                        <input type="text" name="seg_nombre" id="seg_nombre" value="{{ old('seg_nombre') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Apellidos -->
                    <div>
                        <label for="pri_apellido" class="block text-sm font-medium text-gray-700">Primer Apellido</label>
                        <input type="text" name="pri_apellido" id="pri_apellido" required value="{{ old('pri_apellido') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="seg_apellido" class="block text-sm font-medium text-gray-700">Segundo Apellido</label>
                        <input type="text" name="seg_apellido" id="seg_apellido" required value="{{ old('seg_apellido') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div>
                        <label for="fec_nacimiento" class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
                        <input type="date" name="fec_nacimiento" id="fec_nacimiento" required value="{{ old('fec_nacimiento') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Sexo -->
                    <div>
                        <label for="sex_usuario" class="block text-sm font-medium text-gray-700">Sexo</label>
                        <select name="sex_usuario" id="sex_usuario" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccione...</option>
                            <option value="Masculino" {{ old('sex_usuario') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Femenino" {{ old('sex_usuario') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>

                    <!-- Celular -->
                    <div>
                        <label for="cel_usuario" class="block text-sm font-medium text-gray-700">Celular</label>
                        <input type="tel" name="cel_usuario" id="cel_usuario" required value="{{ old('cel_usuario') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Correo -->
                    <div>
                        <label for="correo_usuario" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" name="correo_usuario" id="correo_usuario" required value="{{ old('correo_usuario') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="contraseña" class="block text-sm font-medium text-gray-700">Contraseña</label>
                        <input type="password" name="contraseña" id="contraseña" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="contraseña_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                        <input type="password" name="contraseña_confirmation" id="contraseña_confirmation" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Rol -->
                    <div>
                        <label for="rol_usuario" class="block text-sm font-medium text-gray-700">Rol</label>
                        <select name="rol_usuario" id="rol_usuario" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccione...</option>
                            <option value="Arrendador" {{ old('rol_usuario') == 'Arrendador' ? 'selected' : '' }}>Arrendador</option>
                            <option value="Arrendatario" {{ old('rol_usuario') == 'Arrendatario' ? 'selected' : '' }}>Arrendatario</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" 
                            class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Registrarse
                    </button>
                </div>
            </form>

            <p class="mt-4 text-center text-sm text-gray-600">
                ¿Ya tienes una cuenta?
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Inicia sesión aquí
                </a>
            </p>
        </div>
    </div>
</body>
</html>