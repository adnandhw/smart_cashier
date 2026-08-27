import os
from roboflow import Roboflow
from ultralytics import YOLO

# ==========================================================
# PENELITIAN SKRIPSI
# Pengembangan Sistem Kasir Pintar Berbasis Web
# Menggunakan YOLOv8-Nano
# ==========================================================

# ==========================================================
# 1. DOWNLOAD DATASET ROBOFLOW
# ==========================================================

print("=" * 60)
print("DOWNLOAD DATASET")
print("=" * 60)

from roboflow import Roboflow
rf = Roboflow(api_key="YWjQy8V3aLtvK3xst4kH")
project = rf.workspace("adnans-workspace-yy2qt").project("adnan-wahab-2f4ov")
version = project.version(3)
dataset = version.download("yolov8")

print("\nDataset berhasil diunduh")
print(dataset.location)

# ==========================================================
# 2. LOAD MODEL YOLOv8-NANO
# ==========================================================

print("\n" + "=" * 60)
print("LOAD YOLOv8-NANO")
print("=" * 60)

model = YOLO("yolov8n.pt")

print("Model berhasil dimuat")

# ==========================================================
# 3. TRAINING
# ==========================================================

print("\n" + "=" * 60)
print("TRAINING MODEL")
print("=" * 60)

results = model.train(

    # Dataset
    data=os.path.join(dataset.location, "data.yaml"),

    # ===============================
    # Parameter Penelitian
    # ===============================

    epochs=50,
    imgsz=640,
    batch=16,

    optimizer="SGD",
    lr0=0.01,

    # ===============================
    # Device
    # ===============================

    device="mps",          # Mac Apple Silicon

    workers=0,

    # ===============================
    # Training
    # ===============================

    pretrained=True,

    cache=True,

    patience=20,

    seed=42,

    deterministic=True,

    amp=True,

    # ===============================
    # Data Augmentation
    # ===============================

    degrees=10,

    translate=0.10,

    scale=0.50,

    fliplr=0.50,

    hsv_h=0.015,

    hsv_s=0.70,

    hsv_v=0.40,

    # ===============================
    # Output
    # ===============================

    project="runs/detect",

    name="train",

    exist_ok=True,

    save=True,

    save_period=10,

    verbose=True,

    plots=True,

    val=True
)

print("\nTraining selesai")

# ==========================================================
# 4. LOAD MODEL TERBAIK
# ==========================================================

print("\n" + "=" * 60)
print("LOAD BEST MODEL")
print("=" * 60)

best_model = YOLO("runs/detect/train/weights/best.pt")

print("Best model berhasil dimuat")

# ==========================================================
# 5. VALIDASI MODEL
# ==========================================================

print("\n" + "=" * 60)
print("VALIDASI MODEL")
print("=" * 60)

metrics = best_model.val()

print("\nHASIL EVALUASI")

print("-" * 50)

print(f"Precision : {metrics.box.mp:.4f}")

print(f"Recall    : {metrics.box.mr:.4f}")

print(f"mAP50     : {metrics.box.map50:.4f}")

print(f"mAP50-95  : {metrics.box.map:.4f}")

print("-" * 50)

# ==========================================================
# 6. EXPORT KE ONNX
# ==========================================================

print("\n" + "=" * 60)
print("EXPORT MODEL KE ONNX")
print("=" * 60)

best_model.export(

    format="onnx",

    imgsz=640,

    simplify=True

)

print("Export ONNX selesai")

# ==========================================================
# 7. LOKASI FILE
# ==========================================================

print("\n" + "=" * 60)
print("FILE HASIL")
print("=" * 60)

print("Model Terbaik : runs/detect/train/weights/best.pt")

print("Model Terakhir: runs/detect/train/weights/last.pt")

print("Model ONNX    : runs/detect/train/weights/best.onnx")

print("\nPenelitian selesai.")