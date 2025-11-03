import os
from PIL import Image
import pillow_avif  # Required for AVIF support in Pillow

def convert_images_in_current_folder(quality=75):
    """
    Finds and converts JPG, JPEG, and PNG images in the current directory to AVIF format,
    saving them in a newly created 'output' subfolder.
    """
    # Get the directory where the script is located
    source_dir = os.getcwd()
    output_dir = os.path.join(source_dir, 'output')

    # Create the output directory if it doesn't exist
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)
        print(f"Created directory: {output_dir}")

    supported_formats = ('.jpg', '.jpeg', '.png')

    # List all files in the current directory
    for filename in os.listdir(source_dir):
        if filename.lower().endswith(supported_formats):
            file_path = os.path.join(source_dir, filename)
            
            # Define the output path for the new AVIF image
            file_name_without_ext, _ = os.path.splitext(filename)
            avif_path = os.path.join(output_dir, file_name_without_ext + '.avif')

            try:
                with Image.open(file_path) as img:
                    print(f"Converting {filename} to AVIF...")
                    img.save(avif_path, 'AVIF', quality=quality)
                    print(f"Successfully saved to {avif_path}")

            except Exception as e:
                print(f"Could not convert {filename}: {e}")

if __name__ == '__main__':
    # --- Configuration ---
    # Set your desired quality level (0-100). Higher means better quality and larger file size.
    # A value around 75 is generally a good starting point for web optimization.
    quality_level = 75
    # -------------------

    print("Starting image conversion...")
    convert_images_in_current_folder(quality=quality_level)
    print("\nConversion process finished. Check the 'output' folder.")