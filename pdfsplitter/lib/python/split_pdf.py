#!/usr/bin/env python3
import sys
from pypdf import PdfReader, PdfWriter
import os

def split_pdf(input_path, output_dir):
    try:
        # Create PDF reader object
        reader = PdfReader(input_path)
        
        # Split each page
        for i in range(len(reader.pages)):
            writer = PdfWriter()
            writer.add_page(reader.pages[i])
            
            output_path = os.path.join(output_dir, f'split_{i+1}.pdf')
            with open(output_path, 'wb') as output_file:
                writer.write(output_file)
                
        return True
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)
        return False

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: split_pdf.py <input_pdf> <output_dir>")
        sys.exit(1)
        
    input_pdf = sys.argv[1]
    output_dir = sys.argv[2]
    
    success = split_pdf(input_pdf, output_dir)
    sys.exit(0 if success else 1)
