<?php
$doctorControllerPath = 'app/Http/Controllers/Admin/DoctorController.php';
$content = file_get_contents($doctorControllerPath);

$replacement = <<<'EOT'
        $doctor = Doctor::findOrFail($id);
        $doctor->fill($data);

        if (!$doctor->isDirty()) {
            session()->flash('swal', [
                'icon' => 'info',
                'title' => 'Sin cambios',
                'text' => 'No se detectaron cambios en la información del doctor.',
            ]);
            return redirect()->back();
        }

        $doctor->save();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Doctor actualizado!',
            'text' => 'Los datos del doctor se han actualizado correctamente.',
        ]);
        return redirect()->back();
EOT;

$content = preg_replace('/\$doctor\s*=\s*Doctor::findOrFail.*?\n.*?return.*?redirect.*?admin\.doctors\.edit.*?;\n\s*\}/s', $replacement . "\n    }", $content);
file_put_contents($doctorControllerPath, $content);
