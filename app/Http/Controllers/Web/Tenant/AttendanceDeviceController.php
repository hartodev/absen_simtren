<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Models\AttendanceDevice;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/** Device Kiosk -- tabel `attendance_devices`. */
class AttendanceDeviceController extends TenantAdminController
{
    public function index()
    {
        return $this->view('devices.index', [
            'devices' => AttendanceDevice::with('classRoom:id,name')
                ->where('company_id', $this->cid())->latest()->get(),
        ]);
    }

    public function create()
    {
        return $this->view('devices.form', [
            'device'  => new AttendanceDevice(['is_active' => true]),
            'classes' => $this->classes(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $token = AttendanceDevice::newToken();

        AttendanceDevice::create($data + [
            'company_id'    => $this->cid(),
            'device_token'  => $token,
            'registered_by' => Auth::id(),
        ]);

        // Token hanya ditampilkan SEKALI setelah dibuat / di-reset.
        return $this->to('devices.index')
            ->with('success', 'Device didaftarkan.')
            ->with('new_token', $token);
    }

    public function edit(int $id)
    {
        return $this->view('devices.form', [
            'device'  => $this->find($id),
            'classes' => $this->classes(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->find($id)->update($this->validated($request));

        return $this->to('devices.index')->with('success', 'Device diperbarui.');
    }

    public function regenerate(int $id)
    {
        $token = AttendanceDevice::newToken();
        $this->find($id)->update(['device_token' => $token]);

        return $this->to('devices.index')
            ->with('success', 'Token baru dibuat. Token lama langsung tidak berlaku.')
            ->with('new_token', $token);
    }

    public function destroy(int $id)
    {
        $this->find($id)->delete();

        return $this->to('devices.index')->with('success', 'Device dihapus.');
    }

    // ------------------------------------------------------------------

    private function find(int $id): AttendanceDevice
    {
        return AttendanceDevice::where('company_id', $this->cid())->findOrFail($id);
    }

    private function classes()
    {
        return ClassRoom::where('company_id', $this->cid())->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'class_id'          => ['nullable', Rule::exists('class_rooms', 'id')->where('company_id', $this->cid())],
            'device_identifier' => ['nullable', 'string', 'max:255'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
