<template>
	<view>
		<u-button text="登陆" @click="login"></u-button>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				
			};
		},
		methods: {
			async login() {
				const res = await uni.$u.http.get('/user/login')
				uni.setStorageSync('access_token', res.access_token);
				uni.setStorageSync('refresh_token', res.refresh_token);
				uni.navigateTo({
					url: '/pages/index/index',
				});
			}
		}
	}
</script>

<style lang="scss">

</style>