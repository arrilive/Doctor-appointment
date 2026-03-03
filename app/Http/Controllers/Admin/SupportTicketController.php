<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

// Controlador para gestionar los tickets de soporte
class SupportTicketController extends Controller
{
    /**
     * Mostrar la lista de tickets de soporte.
     */
    public function index()
    {
        // Obtener todos los tickets con la relación de usuario, ordenados del más reciente al más antiguo
        $tickets = SupportTicket::with('user')->latest()->get();

        return view('admin.support-tickets.index', compact('tickets'));
    }

    /**
     * Mostrar el formulario para crear un nuevo ticket.
     */
    public function create()
    {
        return view('admin.support-tickets.create');
    }

    /**
     * Guardar un nuevo ticket de soporte.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:2000',
        ]);

        // Crear el ticket asignándolo al usuario autenticado
        SupportTicket::create([
            'user_id'     => auth()->id(),
            'title'       => $data['title'],
            'description' => $data['description'],
            'status'      => 'abierto',
        ]);

        // Mostrar alerta de éxito
        session()->flash('swal', [
            'icon'  => 'success',
            'title' => '¡Ticket creado!',
            'text'  => 'Tu ticket de soporte ha sido enviado correctamente.',
        ]);

        return redirect()->route('admin.support-tickets.index');
    }

    /**
     * Eliminar un ticket de soporte.
     */
    public function destroy(string $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->delete();

        // Mostrar alerta de éxito
        session()->flash('swal', [
            'icon'  => 'success',
            'title' => '¡Ticket eliminado!',
            'text'  => 'El ticket de soporte ha sido eliminado correctamente.',
        ]);

        return redirect()->route('admin.support-tickets.index');
    }
}
