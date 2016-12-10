#include <jni.h>
#include <string>

extern "C"
jstring
Java_br_com_charlessilva_csmobile_MainActivity_stringFromJNI(
        JNIEnv *env,
        jobject /* this */) {
    std::string hello = "Olá de C ++";
    return env->NewStringUTF(hello.c_str());
}
