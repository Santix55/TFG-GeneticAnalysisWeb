#include <string>
#include <iostream>
#include <fstream>
using namespace std;

#define uchar unsigned char

int k;  // longitud de las palabras que se quiera medir 
int N;  // tamaño de la imagen de largo y ancho
int NN; // tamaño de la imagen en total

/**
 * Obtiene la firma genomica normalizada
 * @param nombre_entrada Nombre del fichero del genoma
 */
int* firmaNormalizada(string nombre_entrada) {
    int l=0;                // longitud de la cadena actual
    int total = 0;          // número de nucleocitos que hay en total  
    int* firma = nullptr;   // imagen sin normalizar

    // punto actual
    double x = 0.5 * N;
    double y = x; 

    // Apetura del archivo
    ifstream archivo(nombre_entrada);
    if(!archivo.is_open()) {
        cout << "No se pudo leer el archivo "<< nombre_entrada << endl;
        return nullptr;
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

    
    // Normalizar la imagen
    const double VALOR_MEDIO = total / double(NN); // equivale al color 127
    const double FACTOR_CONV = 127 / VALOR_MEDIO;

    for (int i=0; i<NN; ++i) {
        //cout << firma[i] << "\t";

        int valor_norm = firma[i] * FACTOR_CONV;                     
        firma[i] = valor_norm > 255 ? 255:
                   valor_norm < 0   ? 0  :
                                      valor_norm;
        
        // cout << firma[i] << "\t" << valor_norm << endl;
    }

    archivo.close();
    return firma;
}

/**
 * Obtiene la firma genomica de un archivo FATSA mediante cálculo
 * @param argv[1] Archivo FATSA de entrada
 * @param argv[2] Archivo PGM de salida en escala de grises 8-bit
 * @param argv[3] Longitud de las palabras a contar (k)
 */
int main(int argc, char* argv[]) {
    string nombre_entrada0;
    string nombre_entrada1;
    string nombre_salida;

    if (argc != 5) {
        // Si no tiene 4 argumentos
        cout << "Debug mode" << endl;
        nombre_entrada0 = "Arabidopsis.fa";
        nombre_entrada1 = "Drosophila_melanogaster.BDGP6.46.dna.primary_assembly.X.fa";
        nombre_salida = "salida.pgm";
        k = 5;
    }
    else {
        // Copiar variables de entrada
        nombre_entrada0 = argv[1];
        nombre_entrada1 = argv[2];
        nombre_salida = argv[3];
        k = stoi(argv[4]);
    }


    // Calcular el tamaño de la imagen a partir de k
    N = 1<<k;
    NN = N*N;

    int* firma0 = firmaNormalizada(nombre_entrada0);
    if(firma0 == nullptr) {return -1;}

    int* firma1 = firmaNormalizada(nombre_entrada1);
    if(firma1 == nullptr) {return -2;}

    // Calcuar la diferencia con zoom
    ofstream fout (nombre_salida);
    if(!fout.is_open()) {
        cout << "No se pudó crear archivo de salida de nombre " << nombre_salida;
        return -3;
    }

    const int ZOOM = k<10 ? 1<<(10-k) : 1;  // zoom de un lado

    // Cabecera
    fout << "P5\n";                          // Modo P5 blanco y negro
    fout << N*ZOOM << " " << N*ZOOM<< endl;  // Resoulción N x N
    fout << 255 << endl;                     // Número de grises

    // Píxeles
    for(int ny=0; ny<NN; ny+=N) {                // Recorrer filas
        for(int zoomy=0; zoomy<ZOOM; ++zoomy) {     // Repetirlas ZOOM veces
            for(int nx=0; nx<N; ++nx) {             // Recorrer cada pixel
                int i = nx+ny;
                uchar pixel = (firma1[i] - firma0[i] + 255) >> 1;
                for(int zoomx=0; zoomx<ZOOM; ++zoomx)  // Recorrer cada pixel ZOOM 
                    fout << pixel;
                // cout << firma0[i] << "\t" << firma1[i] << "\t" << (int) pixel << endl;
            }
        }
    }

    fout.close();
    delete [] firma0;
    delete [] firma1;

    return 0;
}
