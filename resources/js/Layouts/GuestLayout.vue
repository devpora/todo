<template>
  <div
    class="bg-white flex w-full min-h-screen flex-row justify-center align-center"
  >
    <div
      class="hidden sm:block sm:w-1/4 md:w-1/2 bg-cover bg-[url('/images/bg_w.png')]"
    />
    <div
      class="w-full sm:w-3/4 md:w-1/2 bg-white flex flex-col justify-center self-center"
    >
      <div class="bg-white w-1/2 mx-auto rounded-xl flex flex-col gap-y-4">
        <slot />
      </div>
    </div>
  </div>
</template>
