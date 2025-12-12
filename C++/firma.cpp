#include <string>
#include <iostream>
#include <fstream>
using namespace std;

#define uchar unsigned char

/**
 * Obtiene la firma genomica de un archivo FATSA mediante cálculo
 * @param argv[1] Archivo FATSA de entrada
 * @param argv[2] Archivo PGM de salida en escala de grises 8-bit
 * @param argv[3] Longitud de las palabras a contar (k)
 */
int main(int argc, char* argv[]) {
    int k;  // longitud de las palabras que se quiera medir 
    int l=0;// longitud de la cadena actual

    int N;  // tamaño de la imagen de largo y ancho
    int NN; // tamaño de la imagen en total

    int total = 0;  // número de nucleocitos que hay en total  

    int* firma = nullptr;    // imagen sin normalizar

    // Si no tiene 3 argumentos, devuelve 1
    if (argc != 4) {
        cerr << "Uso: " << argv[0] << " <archivo_entrada> <archivo_salida> <k>" << std::endl;
        return 1; // Salir con un código de error
    }

    // Copiar variables de entrada
    string nombre_entrada = argv[1];
    string nombre_salida  = argv[2];
    k = stoi(argv[3]);

    // Calcular el tamaño de la imagen a partir de k
    N = 1<<k;
    NN = N*N;

    // punto actual
    double x = 0.5 * N;
    double y = x; 

    // Apetura del archivo
    ifstream archivo(nombre_entrada);
    if(!archivo.is_open()) {
        cout << "No se pudo leer el archivo" << endl;
        return -1;
    }

    string linea;

    // La primera linea siempre es un comentario que se elimina
    getline(archivo, linea);

    // Inicializar los píxeles de la firma a 0
    firma = new int[NN];
    for(int i=0; i<NN; i++)
        firma[i] = 0;

    // Generar la firma sin normalizar cada cada nucleocito
    while (getline(archivo, linea)) {
        
        if (linea[0] == '>') {l=0;} // comentario
        else {total += linea.length();}

        for(auto c: linea) {
            l++;
            if(l<k) continue;

            double x_anterior = x;
            double y_anterior = y;

            switch (c) {
                case 'C':   // arriba - izquierda
                    break;

                case 'G':   // arriba - derecha
                    x+=N;
                    break;
                
                case 'A':   // abajo - izquierda
                    y+=N;
                    break;
                
                case 'T':   // abajo - derecha
                    x+=N;
                    y+=N;
                    break;

                default:    // nucleocito no identificado
                    l=0;
                    continue;
                    break;
            }

            x *= 0.5;
            y *= 0.5;

            int pos_x = int(x);
            int pos_y = int(y);

            // evitar que se salga de rango, por limitaciones de preción del tipo double
            if (pos_x >= N) { pos_x = N-1; }
            if (pos_y >= N) { pos_y = N-1; }

            firma[(pos_y<<k) + pos_x]++;
        }
    }
    
    archivo.close();

    /*
    try {
        archivo.close();
    } catch (const std::exception& e) {
        std::cerr << "Excepción: " << e.what() << std::endl;
    }
    */
    /*
    // Normalizar //
    imagen = new uchar[NN];
    const double VALOR_MEDIO = total / double(NN); // equivale al color 127
    const double FACTOR_CONV = 127 / VALOR_MEDIO;
    for(int i=0; i<NN; ++i) {
        int valor_norm = firma[i] * FACTOR_CONV;
        imagen[i] = valor_norm > 255 ? 255 :
                    valor_norm < 0   ? 0   :
                                       valor_norm;
    }

    // Guardar firma //
    ofstream fout;
    fout.open("resultado.pgm", ios::out);
    fout << "P5\n";                 // Modo P5 blanco y negro
    fout << N << " " << N << endl;  // Resoulción N x N
    for(int i=0; i<NN; ++i) {       // Contenido
        fout << imagen[i];
    }
    */

  
    // Guardar archivo normalizado //
    ofstream fout;
    fout.open(nombre_salida, ios::out);

    const double VALOR_MEDIO = total / double(NN); // equivale al color 127
    const double FACTOR_CONV = 127 / VALOR_MEDIO;

    /* // Sin reescalado
    fout << "P5\n";                 // Modo P5 blanco y negro
    fout << N << " " << N << endl;  // Resoulción N x N
    fout << 255 << endl;            // Número de grises

    
    for(int i=0; i<NN; ++i) {
        int valor_norm = firma[i] * FACTOR_CONV;
        fout << char ( (valor_norm > 255) ? 255 :
                       (valor_norm < 0  ) ? 0   :
                                            valor_norm
                     );
    }
    */

    const int ZOOM = k<10? 1<<(10-k) : 1;  // zoom de un lado

    fout << "P5\n";                          // Modo P5 blanco y negro
    fout << N*ZOOM << " " << N*ZOOM<< endl;  // Resoulción N x N
    fout << 255 << endl;                     // Número de grises

    for(int ny=0; ny<NN; ny+=N) {                // Recorrer filas
        for(int zoomy=0; zoomy<ZOOM; ++zoomy) { // Repetirlas ZOOM veces

            for(int nx=0; nx<N; ++nx) {         // Recorrer cada pixel

                int valor_norm = firma[ny+nx] * FACTOR_CONV;                     
                uchar pixel = valor_norm > 255 ? 255:
                              valor_norm < 0   ? 0  :
                                                valor_norm;

                for(int zoomx=0; zoomx<ZOOM; ++zoomx)  // Recorrer cada pixel ZOOM 
                    fout << pixel;
            }
        }
    }

    fout.close();

    delete [] firma;

    return 0;
}